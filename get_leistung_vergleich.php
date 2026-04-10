<?php

if (!function_exists('utils_connect_sql')) include "utils/_utils.php";
check_login();

$mysqli = utils_connect_sql();

// --- 1. Room parameters ---
$stmt = $mysqli->prepare(
    "SELECT * FROM tabelle_räume
     INNER JOIN tabelle_funktionsteilstellen
       ON tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen
        = tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
     WHERE tabelle_räume.tabelle_projekte_idTABELLE_Projekte = ?
     ORDER BY tabelle_räume.Raumbezeichnung"
);
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$raumparameter = [];
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $raumparameter[$row['idTABELLE_Räume']] = $row;
}

// --- 2. Element parameters indexed by [elId][varIdx][paramId] ---
$stmt = $mysqli->prepare(
    "SELECT tpe.Wert, tpe.Einheit,
            tpe.tabelle_Varianten_idtabelle_Varianten  AS varIdx,
            tpe.tabelle_elemente_idTABELLE_Elemente    AS elId,
            tpk.idTABELLE_Parameter_Kategorie          AS catId,
            tp.idTABELLE_Parameter                     AS paramId
     FROM tabelle_parameter_kategorie tpk
     INNER JOIN tabelle_parameter tp
       ON tp.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie = tpk.idTABELLE_Parameter_Kategorie
     INNER JOIN tabelle_projekt_elementparameter tpe
       ON tpe.tabelle_parameter_idTABELLE_Parameter = tp.idTABELLE_Parameter
     WHERE tpe.tabelle_projekte_idTABELLE_Projekte = ?
       AND tp.`Bauangaben relevant` = 1"
);
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$epIndex = []; // [elId][varIdx][paramId] => row
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $epIndex[(int)$row['elId']][(int)$row['varIdx']][(int)$row['paramId']] = $row;
}

// --- 3. All room-element assignments for this project in one query ---
$stmt = $mysqli->prepare(
    "SELECT rhe.TABELLE_Räume_idTABELLE_Räume        AS roomID,
            e.idTABELLE_Elemente,
            e.Bezeichnung,
            v.Variante,
            rhe.tabelle_Varianten_idtabelle_Varianten AS varIdx,
            SUM(rhe.Anzahl)                           AS Anzahl
     FROM tabelle_räume_has_tabelle_elemente rhe
     INNER JOIN tabelle_elemente e
       ON rhe.TABELLE_Elemente_idTABELLE_Elemente = e.idTABELLE_Elemente
     INNER JOIN tabelle_varianten v
       ON rhe.tabelle_Varianten_idtabelle_Varianten = v.idtabelle_Varianten
     INNER JOIN tabelle_räume r
       ON rhe.TABELLE_Räume_idTABELLE_Räume = r.idTABELLE_Räume
     WHERE rhe.Verwendung = 1
       AND r.tabelle_projekte_idTABELLE_Projekte = ?
     GROUP BY rhe.TABELLE_Räume_idTABELLE_Räume,
              e.idTABELLE_Elemente, e.Bezeichnung,
              v.Variante,
              rhe.tabelle_Varianten_idtabelle_Varianten,
              rhe.`Neu/Bestand`
     HAVING SUM(rhe.Anzahl) > 0
     ORDER BY rhe.TABELLE_Räume_idTABELLE_Räume, e.Bezeichnung"
);
$stmt->bind_param("i", $_SESSION["projectID"]);
$stmt->execute();
$elementsByRoom = [];
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $elementsByRoom[(int)$row['roomID']][] = $row;
}

$mysqli->close();

// --- Helpers ---
function unitMult(string $t): float {
    if (stripos($t, 'k') !== false) return 1000.0;
    if (stripos($t, 'M') !== false) return 1000000.0;
    if (stripos($t, 'm') !== false) return 0.001;
    return 1.0;
}
function parseW(string $wert, string $einheit): float {
    $v = floatval(str_replace(",", ".", preg_replace("/[^0-9.,\-]/", "", $wert)));
    return $v * unitMult($einheit);
}
function getNAs(string $s): array {
    $valid = ["AV","SV","ZSV","USV"];
    return array_values(array_filter(explode("/", $s), fn($c) => in_array($c, $valid)));
}

$naMap = ["AV"=>1,"SV"=>2,"ZSV"=>3,"USV"=>4];

// --- Build output ---
$output = [];

foreach ($raumparameter as $roomID => $rp) {
    $P_i = array_fill(0, 5, 0.0); // inkl GLZ [0=noNA,1=AV,2=SV,3=ZSV,4=USV]
    $P_e = array_fill(0, 5, 0.0); // exkl GLZ
    $abw_i = 0.0;
    $abw_e = 0.0;
    $elRows = [];

    foreach ($elementsByRoom[$roomID] ?? [] as $el) {
        $anzahl = floatval($el['Anzahl']);
        $elId   = (int)$el['idTABELLE_Elemente'];
        $varIdx = (int)$el['varIdx'];

        $P = 0.0; $GLZ = 1.0; $NAs = []; $abw = 0.0;

        foreach ($epIndex[$elId][$varIdx] ?? [] as $paramId => $pi) {
            $cat = (int)$pi['catId'];
            if ($cat === 2) {                          // Elektro
                if ($paramId === 18)  $P   = parseW($pi['Wert'], $pi['Einheit']);
                if ($paramId === 82)  $NAs = array_unique(array_merge(getNAs($pi['Wert']), getNAs($pi['Einheit'])));
                if ($paramId === 133) { $GLZ = parseW($pi['Wert'], ''); if ($GLZ <= 0) $GLZ = 1.0; }
            }
            if ($cat === 3 && $paramId === 9)          // HKLS Abwärme
                $abw = parseW($pi['Wert'], $pi['Einheit']);
        }

        $pi_tot = $P * $GLZ * $anzahl;
        $pe_tot = $P * $anzahl;

        if ($pi_tot > 0 || $pe_tot > 0) {
            if (empty($NAs)) {
                $P_i[0] += $pi_tot; $P_e[0] += $pe_tot;
            } else {
                $cnt = count($NAs);
                foreach ($NAs as $na) {
                    $idx = $naMap[$na] ?? 0;
                    $P_i[$idx] += $pi_tot / $cnt;
                    $P_e[$idx] += $pe_tot / $cnt;
                }
            }
        }
        $abw_i += $abw * $GLZ * $anzahl;
        $abw_e += $abw * $anzahl;

        if ($P > 0 || $abw > 0) {
            $elRows[] = [
                'name'     => $el['Bezeichnung'],
                'anzahl'   => $anzahl,
                'variante' => $el['Variante'],
                'P_W'      => round($P),
                'GLZ'      => $GLZ,
                'NAs'      => array_values($NAs),
                'P_inkl'   => round($pi_tot),
                'P_exkl'   => round($pe_tot),
                'abw'      => round($abw),
                'abw_inkl' => round($abw * $GLZ * $anzahl),
                'abw_exkl' => round($abw * $anzahl),
            ];
        }
    }

    $output[] = [
        'id'           => $roomID,
        'name'         => $rp['Raumbezeichnung'] ?? '',
        'nr'           => $rp['Raumnr'] ?? '',
        'elements'     => $elRows,
        // Elemente-Summen
        'el_AV_inkl'   => round($P_i[1]),
        'el_SV_inkl'   => round($P_i[2]),
        'el_ZSV_inkl'  => round($P_i[3]),
        'el_USV_inkl'  => round($P_i[4]),
        'el_noNA_inkl' => round($P_i[0]),
        'el_sum_inkl'  => round(array_sum($P_i)),
        'el_AV_exkl'   => round($P_e[1]),
        'el_SV_exkl'   => round($P_e[2]),
        'el_ZSV_exkl'  => round($P_e[3]),
        'el_USV_exkl'  => round($P_e[4]),
        'el_noNA_exkl' => round($P_e[0]),
        'el_sum_exkl'  => round(array_sum($P_e)),
        'el_abw_inkl'  => round($abw_i),
        'el_abw_exkl'  => round($abw_e),
        // Raumparameter
        'rp_AV'        => (int)($rp['ET_Anschlussleistung_AV_W'] ?? 0),
        'rp_SV'        => (int)($rp['ET_Anschlussleistung_SV_W'] ?? 0),
        'rp_ZSV'       => (int)($rp['ET_Anschlussleistung_ZSV_W'] ?? 0),
        'rp_USV'       => (int)($rp['ET_Anschlussleistung_USV_W'] ?? 0),
        'rp_sum'       => (int)($rp['ET_Anschlussleistung_W'] ?? 0),
        'rp_abw'       => (int)($rp['HT_Waermeabgabe_W'] ?? 0),
    ];
}

header('Content-Type: application/json');
echo json_encode($output);
?>