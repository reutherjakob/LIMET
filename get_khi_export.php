<?php
/**
 * gpmt_export.php  —  KHI BT1/BT2 GPMT-Export (Raumbuch → Revit) als XLSX
 * --------------------------------------------------------------------------
 * Erzeugt das Export-File mit AKTUELLEN Daten aus der DB.
 *
 * Aufruf:
 *   gpmt_export.php                      -> HTML-VORSCHAU (Bootstrap + DataTables)
 *   gpmt_export.php?bt=BT1               -> Vorschau nur BT1
 *   gpmt_export.php?download=1           -> XLSX-Download (Standard BT1+BT2)
 *   gpmt_export.php?download=1&bt=BT1,BT2-> XLSX-Download gefiltert
 *
 * Kein PhpSpreadsheet/Composer nötig – reiner PHP-XLSX-Writer (braucht nur die
 * zip-Erweiterung, die praktisch überall vorhanden ist).
 *
 * WICHTIG: Wir puffern jeglichen Output (ob_start), da init_page_serversides()
 * / Includes Bytes ausgeben können. Vor dem XLSX-Download werden ALLE Buffer
 * verworfen, sonst landet HTML/Whitespace vor der Binärdatei und Excel kann das
 * File nicht mehr öffnen.
 *
 * ====== PROJEKT-SPEZIFISCHE KONFIGURATION (KHI) ===========================
 * Falls sich Element-IDs / Parameter-IDs / Bauabschnitte ändern, hier anpassen.
 */

ob_start();

require_once 'utils/_utils.php';
init_page_serversides();

$projectID = (int)($_SESSION['projectID'] ?? 0);
if (!$projectID) {
    http_response_code(400);
    exit('Kein Projekt in der Session.');
}

// --- Ausgabemodus: Vorschau (Standard) oder Download ---------------------
$DOWNLOAD = isset($_GET['download']) && $_GET['download'] !== '0';

// --- Bauabschnitte (Filter) ---------------------------------------------
$DEFAULT_BT = ['BT1', 'BT2'];
$bt = isset($_GET['bt']) ? array_values(array_filter(array_map('trim', explode(',', $_GET['bt'])))) : $DEFAULT_BT;
$bt = array_values(array_intersect($bt, ['BT1', 'BT2', 'BT3', 'BT4', 'BT5'])); // whitelist
if (!$bt) $bt = $DEFAULT_BT;

// --- Element-Gruppen für die MT_ELEM_* Mengen (idTABELLE_Elemente) -------
$ELEM_GROUPS = [
    'Spuelbecken_MT' => [812, 1768, 1964, 1969],
    'Ausgussbecken_MT' => [1905, 1465],
    'Desinfektionsmittel_zumischgeraet' => [10],
    'Leibschuesselspueler' => [2, 3, 4],
    'Hockerausguss' => [112],
];

// --- Parameter-IDs für die Leistungs-Summen (Query 2) -------------------
$PARAM_LEISTUNG = 18;   // Leistung (Wert+Einheit kW/MW/W)
$PARAM_GLEICH = 133;  // Gleichzeitigkeitsfaktor
$PARAM_NETZ = 82;   // Netzform (AV/SV/ZSV/USV/Akku...)
$PARAM_GLT = 109;  // GLT (Gebäudeleittechnik) -> ZLT-Datenpunkt-Zählung

// ==========================================================================

$mysqli = utils_connect_sql();
$mysqli->set_charset('utf8mb4');

$btPlaceholders = implode(',', array_fill(0, count($bt), '?'));
$btTypes = str_repeat('s', count($bt));

/* ---------- QUERY 1: Raumdaten + Elementmengen --------------------------- */
$elemCase = '';
foreach ($ELEM_GROUPS as $alias => $ids) {
    $inList = implode(',', array_map('intval', $ids));
    $elemCase .= "SUM(CASE WHEN re.TABELLE_Elemente_idTABELLE_Elemente IN ($inList) THEN re.Anzahl ELSE 0 END) AS `$alias`,\n";
}

$sql1 = "
SELECT
    r.idTABELLE_Räume,
    r.Raumnr,
    r.Raumbezeichnung,
    r.`Raumbereich Nutzer`,
    r.Bauabschnitt,
    r.Entfallen,
    r.`MT-relevant`,
    r.`Anmerkung BauStatik`,
    r.`Fussboden OENORM B5220`,
    r.`Allgemeine Hygieneklasse`,
    r.Strahlenanwendung,
    r.Abdunkelbarkeit,
    r.Anwendungsgruppe,
    r.ET_Anschlussleistung_W            AS RaumAnschlussleistungOhneGLZ,
    r.`ET_RJ45-Ports`,
    r.`EL_Laser 16A CEE Stk`,
    r.`EL_Roentgen 16A CEE Stk`,
    r.`Anmerkung Elektro`,
    r.H6020,
    r.HT_Abluft_Digestorium_Stk,
    r.HT_Abluft_Sicherheitsschrank_Stk,
    r.HT_Abluft_Sicherheitsschrank_Unterbau_Stk,
    r.HT_Punktabsaugung_Stk,
    r.HT_Waermeabgabe_W,
    r.VE_Wasser,
    r.HT_Notdusche,
    r.`HT_Raumtemp Sommer °C`,
    r.`HT_Raumtemp Winter °C`,
    r.`Anmerkung HKLS`,
    r.`1 Kreis O2`,
    r.`2 Kreis O2`,
    r.`1 Kreis DL-5`,
    r.`2 Kreis DL-5`,
    r.`1 Kreis Va`,
    r.`2 Kreis Va`,
    r.NGA,
    r.CO2,
    r.N2O,
    r.`DL-10`,
    r.`DL-tech`,
    $elemCase
    1 AS _dummy
FROM tabelle_räume AS r
LEFT JOIN tabelle_räume_has_tabelle_elemente AS re
       ON re.TABELLE_Räume_idTABELLE_Räume = r.idTABELLE_Räume
WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
  AND r.Bauabschnitt IN ($btPlaceholders)
  AND r.Entfallen <> 1
GROUP BY r.idTABELLE_Räume
ORDER BY r.idTABELLE_Räume
";

$stmt = $mysqli->prepare($sql1);
if (!$stmt) {
    http_response_code(500);
    exit('SQL1 Fehler: ' . $mysqli->error);
}
$types1 = 'i' . $btTypes;
$params1 = array_merge([$projectID], $bt);
$stmt->bind_param($types1, ...$params1);
$stmt->execute();
$rows1 = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ---------- QUERY 2: Leistungssummen je Netzform + GLT-Zählung ----------- */
$leistCalc = "
    COALESCE(re.Anzahl,0) *
    CASE
        WHEN pep_leistung.Einheit='kW' THEN COALESCE(CAST(NULLIF(pep_leistung.Wert,'') AS DECIMAL(10,2)),0)*1000
        WHEN pep_leistung.Einheit='MW' THEN COALESCE(CAST(NULLIF(pep_leistung.Wert,'') AS DECIMAL(10,2)),0)*1000000
        ELSE COALESCE(CAST(NULLIF(pep_leistung.Wert,'') AS DECIMAL(10,2)),0)
    END *
    COALESCE(CASE
        WHEN pep_gleich.Wert IS NULL OR pep_gleich.Wert='' THEN 1.0
        WHEN pep_gleich.Wert='0' THEN 0.0
        ELSE CAST(REPLACE(pep_gleich.Wert,',','.') AS DECIMAL(5,2))
    END, 1.0)
";

// GLT-Datenpunkte = Summe der Element-Anzahl, bei denen der GLT-Parameter
// einen "Ja"-Wert hat (alles ausser leer / 0 / nein / no / false).
// Hinweis: zählt Geräte-Stückzahl. Soll stattdessen die Anzahl *verschiedener*
// Elemente gezählt werden -> re.Anzahl durch 1 ersetzen.
$gltCalc = "
    CASE
        WHEN pep_glt.Wert IS NOT NULL
         AND TRIM(pep_glt.Wert) <> ''
         AND LOWER(TRIM(pep_glt.Wert)) NOT IN ('0','nein','no','false','-','n')
        THEN COALESCE(re.Anzahl,0)
        ELSE 0
    END
";

$sql2 = "
SELECT r.idTABELLE_Räume,
  ROUND(SUM(CASE WHEN pep_netz.Wert IN ('AV','Akku','/Akku','AV/Akku') THEN $leistCalc ELSE 0 END),0) AS MT_Leistung_AV_W,
  ROUND(SUM(CASE WHEN pep_netz.Wert IN ('SV','SV/Akku')               THEN $leistCalc ELSE 0 END),0) AS MT_Leistung_SV_W,
  ROUND(SUM(CASE WHEN pep_netz.Wert IN ('ZSV','ZSV/Akku')             THEN $leistCalc ELSE 0 END),0) AS MT_Leistung_ZSV_W,
  ROUND(SUM(CASE WHEN pep_netz.Wert IN ('USV','USV/Akku')             THEN $leistCalc ELSE 0 END),0) AS MT_Leistung_USV_W,
  SUM($gltCalc) AS MT_GLT_Anzahl
FROM tabelle_räume r
LEFT JOIN tabelle_räume_has_tabelle_elemente re ON re.TABELLE_Räume_idTABELLE_Räume = r.idTABELLE_Räume
LEFT JOIN tabelle_elemente e ON re.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
LEFT JOIN tabelle_varianten v ON re.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
LEFT JOIN tabelle_projekt_elementparameter pep_leistung ON
       pep_leistung.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
   AND pep_leistung.tabelle_elemente_idTABELLE_Elemente   = e.idTABELLE_Elemente
   AND pep_leistung.tabelle_projekte_idTABELLE_Projekte   = ?
   AND pep_leistung.tabelle_parameter_idTABELLE_Parameter = ?
LEFT JOIN tabelle_projekt_elementparameter pep_gleich ON
       pep_gleich.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
   AND pep_gleich.tabelle_elemente_idTABELLE_Elemente   = e.idTABELLE_Elemente
   AND pep_gleich.tabelle_projekte_idTABELLE_Projekte   = ?
   AND pep_gleich.tabelle_parameter_idTABELLE_Parameter = ?
LEFT JOIN tabelle_projekt_elementparameter pep_netz ON
       pep_netz.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
   AND pep_netz.tabelle_elemente_idTABELLE_Elemente   = e.idTABELLE_Elemente
   AND pep_netz.tabelle_projekte_idTABELLE_Projekte   = ?
   AND pep_netz.tabelle_parameter_idTABELLE_Parameter = ?
LEFT JOIN tabelle_projekt_elementparameter pep_glt ON
       pep_glt.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
   AND pep_glt.tabelle_elemente_idTABELLE_Elemente   = e.idTABELLE_Elemente
   AND pep_glt.tabelle_projekte_idTABELLE_Projekte   = ?
   AND pep_glt.tabelle_parameter_idTABELLE_Parameter = ?
WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
  AND r.Bauabschnitt IN ($btPlaceholders)
  AND r.Entfallen <> 1
GROUP BY r.idTABELLE_Räume
";

$stmt = $mysqli->prepare($sql2);
if (!$stmt) {
    http_response_code(500);
    exit('SQL2 Fehler: ' . $mysqli->error);
}
// 4 Parameter-Joins à (projectID, paramID) = 8x 'i', dann WHERE projectID = 1x 'i', dann BT
$types2 = str_repeat('i', 8) . 'i' . $btTypes;
$params2 = array_merge(
    [$projectID, $PARAM_LEISTUNG,
        $projectID, $PARAM_GLEICH,
        $projectID, $PARAM_NETZ,
        $projectID, $PARAM_GLT,
        $projectID],
    $bt
);
$stmt->bind_param($types2, ...$params2);
$stmt->execute();
$res2 = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();

// Leistungen nach RaumID indexieren
$leistById = [];
foreach ($res2 as $r) $leistById[$r['idTABELLE_Räume']] = $r;

/* ---------- Spalten-Mapping: RESULTAT-Spalte (1..50) → Quelle ------------ */
// [Quelle, Schlüssel]   Quelle: 'q1' | 'q2' | null(=leer)
$MAP = [
    1 => ['q1', 'idTABELLE_Räume'],
    2 => ['q1', 'Raumbezeichnung'],
    3 => ['q1', 'Raumbereich Nutzer'],
    4 => ['q1', 'Bauabschnitt'],
    5 => ['q1', 'Entfallen'],
    6 => ['q1', 'MT-relevant'],
    7 => ['q1', 'Raumnr'],
    8 => ['q1', 'Spuelbecken_MT'],
    9 => ['q1', 'Ausgussbecken_MT'],
    10 => ['q1', 'Desinfektionsmittel_zumischgeraet'],
    11 => ['q1', 'Leibschuesselspueler'],
    12 => ['q1', 'Hockerausguss'],
    13 => ['q1', 'Anmerkung BauStatik'],
    14 => ['q1', 'Fussboden OENORM B5220'],
    15 => ['q1', 'Allgemeine Hygieneklasse'],
    16 => ['q1', 'Strahlenanwendung'],
    17 => ['q1', 'Abdunkelbarkeit'],
    18 => ['q1', 'Anwendungsgruppe'],
    19 => ['q1', 'RaumAnschlussleistungOhneGLZ'],
    20 => ['q2', 'MT_Leistung_AV_W'],
    21 => ['q2', 'MT_Leistung_SV_W'],
    22 => ['q2', 'MT_Leistung_ZSV_W'],
    23 => ['q2', 'MT_Leistung_USV_W'],
    24 => ['q1', 'ET_RJ45-Ports'],           // RJ45 (war leer)
    25 => ['q1', 'EL_Laser 16A CEE Stk'],     // Laser (war leer)
    26 => ['q1', 'EL_Roentgen 16A CEE Stk'],  // Röntgen (war leer)
    27 => ['q1', 'Anmerkung Elektro'],
    28 => ['q1', 'H6020'],
    29 => ['q1', 'HT_Abluft_Digestorium_Stk'],
    30 => ['q1', 'HT_Abluft_Sicherheitsschrank_Stk'],
    31 => ['q1', 'HT_Abluft_Sicherheitsschrank_Unterbau_Stk'],
    32 => ['q1', 'HT_Punktabsaugung_Stk'],
    33 => ['q1', 'HT_Waermeabgabe_W'],
    34 => ['q1', 'VE_Wasser'],
    35 => ['q1', 'HT_Notdusche'],             // Notdusche (war leer)
    36 => ['q1', 'HT_Raumtemp Sommer °C'],     // Max-Raumtemp (war leer)
    37 => ['q1', 'HT_Raumtemp Winter °C'],     // Min-Raumtemp (war leer)
    38 => ['q1', 'Anmerkung HKLS'],
    39 => ['q1', '1 Kreis O2'],
    40 => ['q1', '2 Kreis O2'],
    41 => ['q1', '1 Kreis Va'],
    42 => ['q1', '2 Kreis Va'],
    43 => ['q1', '1 Kreis DL-5'],
    44 => ['q1', '2 Kreis DL-5'],
    45 => ['q1', 'NGA'],
    46 => ['q1', 'DL-tech'],
    47 => ['q1', 'N2O'],
    48 => ['q1', 'CO2'],
    49 => ['q1', 'DL-10'],
    50 => ['q2', 'MT_GLT_Anzahl'],            // ZLT Datenpunkt = Anzahl GLT-Elemente
];
// Spalten die IMMER als Text geschrieben werden (sonst auto: numerisch → Zahl)
$FORCE_TEXT = [2 => 1, 3 => 1, 4 => 1, 7 => 1, 13 => 1, 14 => 1, 15 => 1, 18 => 1, 27 => 1, 28 => 1, 38 => 1];

/* ---------- Datenzeilen für RESULTAT bauen ------------------------------- */
$dataRows = [];
foreach ($rows1 as $r) {
    $id = $r['idTABELLE_Räume'];
    $q2 = $leistById[$id] ?? [];
    $row = [];
    for ($c = 1; $c <= 50; $c++) {
        [$srcKind, $key] = $MAP[$c];
        if ($srcKind === null) {
            $row[$c] = ['', false];
            continue;
        }
        $val = $srcKind === 'q1' ? ($r[$key] ?? null) : ($q2[$key] ?? null);

        if ($val === null || $val === '') {
            $row[$c] = ['', false];
            continue;
        }

        $forceText = isset($FORCE_TEXT[$c]);
        if (!$forceText && is_numeric($val)) {
            // Zahl: Ganzzahl bleibt Ganzzahl
            $num = $val + 0;
            $row[$c] = [(is_float($num) && $num == (int)$num) ? (int)$num : $num, true];
        } else {
            $row[$c] = [(string)$val, false];
        }
    }
    $dataRows[] = $row;
}

/* ---------- Header-Definition für das Sheet "RESULTAT" ------------------- */
$RES_H = json_decode(<<<'JSON_RES'
{"1": {"8": "Allgemein", "18": "Elektro", "28": "Haustechnik", "39": "Med.Gase", "50": "nicht notwendig?"}, "2": {"2": "Parameter-bezeichnung Revit", "7": "Nummer", "8": "MT_ELEM_Spülbecken", "9": "MT_ELEM_Ausgussbecken_und_Waschrinne", "10": "MT_ELEM_Desinfektionsmittel-\nzumischgerät", "11": "MT_ELEM_Leibschüsselspüler ", "12": "MT_ELEM_Hockerausguss ", "13": "MT_ALLG_Statikanforderung", "14": "MT_ALLG_ÖNORM-B5220", "15": "MT_ALLG_Hygieneklasse", "16": "MT_ALLG_Strahlenanwendung", "17": "MT_ALLG_Abdunkelbarkeit", "18": "MT_ET_ÖVE-E8101", "19": "MT_ET_RaumAnschlusslOhneGlz", "20": "MT_ET_AV", "21": "MT_ET_SV", "22": "MT_ET_ZSV", "23": "MT_ET_USV", "24": "MT_ET_RJ45-Ports", "25": "MT_ET_CEE16A-Laser", "26": "MT_ET_CEE16A-Röntgen", "27": "MT_ET_Anmerkungen", "28": "MT_HT_ÖNORM-H6020", "29": "MT_HT_Abluft-DigestorWerkbank", "30": "MT_HT_Abluft-Sicherheitsschrank", "31": "MT_HT_Abluft-SicherheitsschrankUnterbau", "32": "MT_HT_Punktabsaugung", "33": "MT_HT_AbwärmeMT", "34": "MT_HT_VE-Wasser", "35": "MT_HT_Notdusche", "36": "MT_HT_Max-Raumtemperatur", "37": "MT_HT_Min-Raumtemperatur", "38": "MT_HT_Anmerkungen", "39": "MT_GAS_1-Kreis-O2", "40": "MT_GAS_2-Kreise-O2", "41": "MT_GAS_1-Kreis-Va", "42": "MT_GAS_2-Kreise-Va", "43": "MT_GAS_1-Kreis-DL5", "44": "MT_GAS_2-Kreise-DL5", "45": "MT_GAS_NGA", "46": "MT_GAS_DLtech", "47": "MT_GAS_N2O", "48": "MT_GAS_CO2", "49": "MT_GAS_DL10"}, "3": {"2": "Datentyp", "8": "Ganzzahl", "9": "Ganzzahl", "10": "Ganzzahl", "11": "Ganzzahl", "12": "Ganzzahl", "13": "Text", "14": "Text", "15": "Text", "16": "Text", "17": "Text", "18": "Text", "19": "Ganzzahl", "20": "Ganzzahl", "21": "Ganzzahl", "22": "Ganzzahl", "23": "Ganzzahl", "24": "Ganzzahl", "25": "Ganzzahl", "26": "Ganzzahl", "27": "Text", "28": "Text", "29": "Ganzzahl", "30": "Ganzzahl", "31": "Ganzzahl", "32": "Ganzzahl", "33": "Ganzzahl", "34": "Ja/Nein", "35": "Text", "36": "Ganzzahl", "37": "Ganzzahl", "38": "Text", "39": "Ja/Nein", "40": "Ja/Nein", "41": "Ja/Nein", "42": "Ja/Nein", "43": "Ja/Nein", "44": "Ja/Nein", "45": "Ja/Nein", "46": "Ja/Nein", "47": "Ja/Nein", "48": "Ja/Nein", "49": "Ja/Nein"}, "4": {"2": "Einheit", "8": "Stk.", "9": "Stk.", "10": "Stk.", "11": "Stk.", "12": "Stk.", "19": "Ganzzahl in Watt", "20": "Ganzzahl in Watt", "21": "Ganzzahl in Watt", "22": "Ganzzahl in Watt", "23": "Ganzzahl in Watt", "24": "Stk.", "25": "Stk.", "26": "Stk.", "29": "Stk.", "30": "Stk.", "31": "Stk.", "32": "Stk.", "33": "Ganzzahl in Watt", "36": "Ganzzahl in °C", "37": "Ganzzahl in °C"}, "5": {"2": "Bezeichnung Raumbuch", "7": "Raumnummer", "8": "Spülbecken MT ", "9": "Ausgussbecken MT ", "10": "Desinfektionsmittel-\nzumischgerät", "11": "Leibschüsselspüler ", "12": "Hockerausguss ", "13": "Statikanforderung", "14": "ÖNORM B5220\nleitfähigkeit Böden", "15": "Hygieneklasse", "16": "Strahlenanwendung", "17": "Abdunkelbarkeit", "18": "ÖVE E8101", "19": "Raum Anschlussl. ohne Glz.", "20": "Raum Anschlussl. mit Glz.\nAV", "21": "Raum Anschlussl. mit Glz.\nSV", "22": "Raum Anschlussl. mit Glz.\nZSV", "23": "Raum Anschlussl. mit Glz.\nUSV", "24": "RJ45-Ports\n(IT-Anschluss)", "25": "CEE16A Laser", "26": "CEE16A Röntgen", "27": "Anmerkungen zur\nET-Planung", "28": "ÖNORM H6020\nKrankenhauslüftung", "29": "Abluft Digestor/Werkbank", "30": "Abluft Sicherheitsschrank", "31": "Abluft Sicherheitsschrank Unterbau", "32": "Punktabsaugung", "33": "Abwärme MT", "34": "VE Wasser", "35": "Notdusche", "36": "Max. Raumtemperatur", "37": "Min. Raumtemperatur", "38": "Anmerkungen zur\nHT-Planung", "39": "1 Kreis O2", "40": "2 Kreise O2", "41": "1 Kreis Va", "42": "2 Kreise Va", "43": "1 Kreis DL5", "44": "2 Kreise DL5", "45": "NGA", "46": "DLtech", "47": "N2O", "48": "CO2", "49": "DL10", "50": "ZLT Datenpunkt (medizinisch)"}, "6": {"1": "id LIMET", "16": "0 = Nein;\n1 = Ja;", "17": "0 = Nein;\n1 = Ja;", "35": "derzeit keine im Projekt vorgesehen. Spalte könnte entfallen", "39": "0 = Nein;\n1 = Ja;", "40": "0 = Nein;\n1 = Ja;", "41": "0 = Nein;\n1 = Ja;", "42": "0 = Nein;\n1 = Ja;", "43": "0 = Nein;\n1 = Ja;", "44": "0 = Nein;\n1 = Ja;", "45": "0 = Nein;\n1 = Ja;", "46": "0 = Nein;\n1 = Ja;", "47": "im Projekt kein Thema; könnte entfallen", "48": "0 = Nein;\n1 = Ja;", "49": "0 = Nein;\n1 = Ja;", "50": "Anzahl Elemente mit GLT"}, "7": {"1": "idTABELLE_Räume", "2": "Raumbezeichnung", "3": "Raumbereich Nutzer", "4": "Bauabschnitt", "5": "Entfallen", "6": "MT-relevant", "7": "Nummer", "8": "MT_ELEM_Spülbecken", "9": "MT_ELEM_Ausgussbecken", "10": "MT_ELEM_Desinfektionsmittel-\nzumischgerät", "11": "MT_ELEM_Leibschüsselspüler ", "12": "MT_ELEM_Hockerausguss ", "13": "MT_ALLG_Statikanforderung", "14": "MT_ALLG_ÖNORM-B5220", "15": "MT_ALLG_Hygieneklasse", "16": "MT_ALLG_Strahlenanwendung", "17": "MT_ALLG_Abdunkelbarkeit", "18": "MT_ET_ÖVE-E8101", "19": "MT_ET_RaumAnschlusslOhneGlz", "20": "MT_ET_AV", "21": "MT_ET_SV", "22": "MT_ET_ZSV", "23": "MT_ET_USV", "24": "MT_ET_RJ45-Ports", "25": "MT_ET_CEE16A-Laser", "26": "MT_ET_CEE16A-Röntgen", "27": "MT_ET_Anmerkungen", "28": "MT_HT_ÖNORM-H6020", "29": "MT_HT_Abluft-DigestorWerkbank", "30": "MT_HT_Abluft-Sicherheitsschrank", "31": "MT_HT_Abluft-SicherheitsschrankUnterbau", "32": "MT_HT_Punktabsaugung", "33": "MT_HT_AbwärmeMT", "34": "MT_HT_VE-Wasser", "35": "MT_HT_Notdusche", "36": "MT_HT_Max-Raumtemperatur", "37": "MT_HT_Min-Raumtemperatur", "38": "MT_HT_Anmerkungen", "39": "MT_GAS_1-Kreis-O2", "40": "MT_GAS_2-Kreise-O2", "41": "MT_GAS_1-Kreis-Va", "42": "MT_GAS_2-Kreise-Va", "43": "MT_GAS_1-Kreis-DL5", "44": "MT_GAS_2-Kreise-DL5", "45": "MT_GAS_NGA", "46": "MT_GAS_DLtech", "47": "MT_GAS_N2O", "48": "MT_GAS_CO2", "49": "MT_GAS_DL10"}}
JSON_RES, true);

/* ======================================================================== *
 *  VORSCHAU-MODUS (HTML, Bootstrap + DataTables – kein eigenes CSS)         *
 * ======================================================================== */
if (!$DOWNLOAD) {

    // Lesbares Label je Resultat-Spalte (1..50):
    // Spalten 1-6 = Metadaten (aus Quell-Zeile 7), 7-50 = Raumbuch-Label (Zeile 5)
    $labelFor = function (int $c) use ($RES_H) {
        $clean = function ($s) {
            return trim(str_replace(["\n", "\r"], ' ', (string)$s));
        };
        if ($c <= 6) {
            return $clean($RES_H['7'][(string)$c] ?? "Sp $c");
        }
        return $clean($RES_H['5'][(string)$c] ?? $RES_H['2'][(string)$c] ?? $RES_H['7'][(string)$c] ?? "Sp $c");
    };
    // Revit-Parametername (Zeile 2/7) als Untertitel
    $revitFor = function (int $c) use ($RES_H) {
        $clean = function ($s) {
            return trim(str_replace(["\n", "\r"], ' ', (string)$s));
        };
        return $clean($RES_H['2'][(string)$c] ?? $RES_H['7'][(string)$c] ?? '');
    };

    $btTag = implode(',', $bt);
    $dlHref = '?download=1&bt=' . rawurlencode($btTag);
    $rowCount = count($dataRows);
    $allBtSets = ['BT1' => 'BT1', 'BT2' => 'BT2', 'BT1,BT2' => 'BT1 + BT2'];
    $currentSel = implode(',', $bt);

    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="utf-8">
        <title>GPMT-Export Vorschau</title>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
        <link rel="icon" href="Logo/iphone_favicon.png"/>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
                integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.js"></script>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
              integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
              crossorigin="anonymous" referrerpolicy="no-referrer"/>
        <link href="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.2.1/af-2.7.0/b-3.2.1/b-colvis-3.2.1/b-html5-3.2.1/b-print-3.2.1/cr-2.0.4/date-1.5.5/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-3.0.0/sr-1.4.1/datatables.min.css"
              rel="stylesheet"/>
    </head>
    <body>

    <div id="limet-navbar"></div>
    <div class="container-fluid">
        <div id="card">
            <div id="card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                        <h1 class="h4 mb-1">GPMT-Export</h1>
                        <div class="btn-group" role="group" aria-label="Bauabschnitt-Filter">
                            <?php foreach ($allBtSets as $key => $label): ?>
                                <a href="?bt=<?= rawurlencode($key) ?>"
                                   class="btn btn-sm <?= $currentSel === $key ? 'btn-primary' : 'btn-outline-primary' ?>">
                                    <?= htmlspecialchars($label) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <span class="badge text-bg-secondary">Filter: <?= htmlspecialchars($btTag) ?></span>


                        <?php if ($rowCount === 0): ?>
                            <div class="alert alert-warning">Keine Räume für diesen Filter gefunden.</div>
                        <?php else: ?>
                    </div>

                    <a href="<?= htmlspecialchars($dlHref) ?>" class="btn btn-success">
                        <i class="fas fa-file-excel"></i> XLSX herunterladen
                    </a>
                </div>
            </div>


            <div id="card-body">
                <table id="gpmtTable"
                       class="table table-sm table-bordered table-hover table-striped align-middle small w-100">
                    <thead class="table-light">
                    <tr>
                        <?php for ($c = 1; $c <= 50; $c++): ?>
                            <th class="text-nowrap">
                                <?= htmlspecialchars($labelFor($c)) ?>
                                <small class="d-block text-muted fw-normal"><?= htmlspecialchars($revitFor($c)) ?></small>
                            </th>
                        <?php endfor; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dataRows as $dr): ?>
                        <tr>
                            <?php for ($c = 1; $c <= 50; $c++):
                                [$val, $isNum] = $dr[$c]; ?>
                                <td class="<?= $isNum ? 'text-end font-monospace' : '' ?>">
                                    <?= $val === '' ? '<span class="text-black-50">–</span>' : nl2br(htmlspecialchars((string)$val)) ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>


            </div>
        </div>
    </div>

    <script>
        $(function () {
            $('#gpmtTable').DataTable({
                scrollX: true,
                pageLength: 15,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Alle']],
                order: [], // Ausgangsreihenfolge = DB-Reihenfolge (nach Raum-ID)
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.2.1/i18n/de-DE.json'
                }
            });
        });
    </script>
    </body>
    </html>
    <?php
    exit;
}

/* ======================================================================== *
 *  XLSX-Writer (reines PHP, nur zip-Erweiterung) — nur Sheet "RESULTAT"     *
 * ======================================================================== */

// Vor dem Binär-Output ZWINGEND allen gepufferten Output (init_page_serversides
// / Includes / Whitespace) verwerfen – sonst ist die XLSX-Datei korrupt.
while (ob_get_level()) {
    ob_end_clean();
}

function col_letter($n)
{
    $s = '';
    while ($n > 0) {
        $n--;
        $s = chr(65 + $n % 26) . $s;
        $n = intdiv($n, 26);
    }
    return $s;
}

function xml_esc($t)
{
    return str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $t);
}

/**
 * Baut Worksheet-XML.
 * $rows = [ rowIndex(1-based) => [ colIndex => [value, isNumber] ] ]
 * $headerRowsCount: erste N Zeilen werden fett+wrap (Style 1) dargestellt.
 */
function sheet_xml(array $rows, int $headerRowsCount, int $maxCol, int $freezeRow = 0, int $freezeCol = 0)
{
    $views = '';
    if ($freezeRow > 0 || $freezeCol > 0) {
        $tl = col_letter($freezeCol + 1) . ($freezeRow + 1);
        $views = '<sheetViews><sheetView workbookViewId="0">'
            . '<pane xSplit="' . $freezeCol . '" ySplit="' . $freezeRow . '" topLeftCell="' . $tl . '" activePane="bottomRight" state="frozen"/>'
            . '</sheetView></sheetViews>';
    }
    $out = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
    $out .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
    $out .= $views . '<sheetData>';
    foreach ($rows as $ri => $cols) {
        $out .= '<row r="' . $ri . '">';
        foreach ($cols as $ci => $cell) {
            [$val, $isNum] = $cell;
            $ref = col_letter($ci) . $ri;
            if ($val === '' || $val === null) {
                $out .= '<c r="' . $ref . '"/>';
                continue;
            }
            $style = $ri <= $headerRowsCount ? 1 : 2;   // 1=header bold+wrap, 2=data wrap
            if ($isNum) {
                $style = $ri <= $headerRowsCount ? 1 : 3; // 3=number plain
                $out .= '<c r="' . $ref . '" s="' . $style . '"><v>' . $val . '</v></c>';
            } else {
                $out .= '<c r="' . $ref . '" s="' . $style . '" t="inlineStr"><is><t xml:space="preserve">' . xml_esc((string)$val) . '</t></is></c>';
            }
        }
        $out .= '</row>';
    }
    $out .= '</sheetData></worksheet>';
    return $out;
}

// Header-Map ($h: ['rowStr'=>['colStr'=>val]]) → rows-Array
function header_to_rows(array $h)
{
    $rows = [];
    foreach ($h as $rStr => $cells) {
        $ri = (int)$rStr;
        $rows[$ri] = [];
        foreach ($cells as $cStr => $val) {
            $isNum = is_int($val) || is_float($val);
            $rows[$ri][(int)$cStr] = [$val, $isNum];
        }
    }
    return $rows;
}

// ---- Sheet "RESULTAT" (Header 1-7 + Datenzeilen ab 8) ----
$resRows = header_to_rows($RES_H);
$ri = 8;
foreach ($dataRows as $dr) {
    $resRows[$ri] = $dr;
    $ri++;
}

// ---- XLSX zusammensetzen (Single-Sheet) ----
$styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
    . '<fonts count="2"><font><sz val="10"/><name val="Arial"/></font>'
    . '<font><b/><sz val="10"/><name val="Arial"/></font></fonts>'
    . '<fills count="2"><fill><patternFill patternType="none"/></fill>'
    . '<fill><patternFill patternType="gray125"/></fill></fills>'
    . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
    . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
    . '<cellXfs count="4">'
    . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
    . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment wrapText="1" vertical="top"/></xf>'
    . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment wrapText="1" vertical="top"/></xf>'
    . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment vertical="top"/></xf>'
    . '</cellXfs></styleSheet>';

$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
    . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
    . '<Default Extension="xml" ContentType="application/xml"/>'
    . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
    . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
    . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
    . '</Types>';

$rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
    . '</Relationships>';

$workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
    . '<sheets><sheet name="RESULTAT" sheetId="1" r:id="rId1"/></sheets></workbook>';

$wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
    . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
    . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
    . '</Relationships>';

$sheet1 = sheet_xml($resRows, 7, 50, 7, 7);

$tmp = tempnam(sys_get_temp_dir(), 'xlsx');
$zip = new ZipArchive();
$zip->open($tmp, ZipArchive::OVERWRITE);
$zip->addFromString('[Content_Types].xml', $contentTypes);
$zip->addFromString('_rels/.rels', $rels);
$zip->addFromString('xl/workbook.xml', $workbook);
$zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);
$zip->addFromString('xl/styles.xml', $styles);
$zip->addFromString('xl/worksheets/sheet1.xml', $sheet1);
$zip->close();

$btTag = implode('', $bt);                       // z.B. BT1BT2
$filename = "KHI_{$btTag}_GPMT_EXPORT_" . date('Ymd') . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($tmp));
header('Cache-Control: no-store');
readfile($tmp);
unlink($tmp);
exit;