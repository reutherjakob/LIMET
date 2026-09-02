<?php
/**
 * RaumAbgleich.php
 *
 * Prüft Raumangaben gegen Soll (Raumtyp / Elemente / Freitext) und ALARMIERT nur.
 * Die Seite setzt NICHTS automatisch. Einzelne Werte können über kleine
 * Buttons pro Zeile manuell gesetzt werden.
 *
 * Prüfungen:
 *   1) Digestoren:   Anzahl Digestor-Elemente == "Digestorenanzahl X" aus `Anmerkung Geräte`.
 *   2) Schrank Säure/Lauge (Element 1092)      vs. Soll lt. Raumtyp.
 *   3) Schrank brennbare Flüssigkeiten (1093/1688) vs. Soll lt. Raumtyp.
 *        -> Regel Schränke: Ist < Soll = FEHLER(rot); Ist > Soll = gelb; Ist == Soll = grün.
 *        -> Sonderfall Lager Chemikalien (RT 29): Soll nach Nutzfläche
 *           (< 19 m² -> 1 ; < 34 m² -> 2 ; >= 34 m² -> 3), identisch für beide Schranktypen.
 *   4) Augenduschen == Spülen (beides aus Elementen), sonst Fehler.
 *   5) Waschküche (RT 22): RDG-Anzahl je Achsen (3/6 Achsen -> 2 RDG, größer -> 4 RDG);
 *        Spülen je Achsen (3 Achsen -> 1 Spüle, ab 6 Achsen -> 2 Spülen).
 *   6) Wasser: ist eine Spüle im Raum, müssen Warm-, Kalt- UND VE-Wasser gesetzt sein,
 *        sonst FEHLER (nur Anzeige).
 *        SONDERREGELN je Raumtyp:
 *          - Typ 25: nur Warm- + Kaltwasser erforderlich, KEIN VE-Wasser.
 *          - Typ 23: weder KW/WW noch VE-Wasser erforderlich (Prüfung entfällt).
 *   7) Elemente unerwünscht: Raumtypen 13/27/28 dürfen KEINE Elemente enthalten.
 *   8) Spülen je Achsen (Nicht-Waschküchen): 3/6 Achsen -> max 1 Spüle, 9 -> max 2;
 *        Ausnahme Raum 231 -> 3 Spülen als einziger.
 *
 * Zusätzlich: Spalte „Änderungsanweisungen“ leitet aus den Prüfungen konkrete
 * Handlungstexte ab (z. B. „2× Digestor hinzufügen“). Wasser wird dort NICHT
 * berücksichtigt.
 *
 *      AR_APs beinhaltet die Anzahl der Achsen für diesen Raum.
 */

require_once '../utils/_utils.php';
require_once '../Nutzerumfrage/raumtypen.php';   // definiert $labortypen


/* ═══════════════════════════════════════════════════════════════════════════
   KONFIGURATION  – hier bei Bedarf anpassen
   ═══════════════════════════════════════════════════════════════════════════ */

/** Element-IDs (idTABELLE_Elemente) je Prüf-Gruppe. Editierbar. */
function abgleich_element_ids(): array
{
    return [
        // Digestorien
        'dig' => [727, 1212, 1456, 1600, 1601, 1602, 1603, 1604, 1605, 1606,
            2056, 2057, 2058, 2059, 2060, 2061],

        // Gefahrenstoffsicherheitsschrank – Säuren/Laugen (4.35.30.2)
        'schrank_sl' => [1092],

        // Gefahrenstoffsicherheitsschrank – brennbare Flüssigkeiten
        //   1093 = 4.35.30.3 (Standmodell),  1688 = 4.35.30.5 (Unterbau)
        'schrank_brennbar' => [1093],//, 1688],

        // Augenduschen: 406 = Augendusche, 1737 = Augendusche in Laborspüle
        'augendusche' => [406, 1737],

        // RDG für Waschküchen – Vorauswahl (ohne Endoskop-/Pharma-/Labor-Sonder-RDGs)
        'rdg' => [6, 311, 846, 1037, 1038, 1134, 1153, 1838],

        // SPÜLEN – aus Elementtabelle ermittelt. BITTE mit eurer Liste abgleichen!
        //   Spülbecken + Spülenverbau + Medienzellen mit Spüle.
        'spuele' => [812, 1000, 1435, 1437,
            1576, 1577, 1578, 1579, 1580, 1581,
            1588, 1589, 1590, 1591, 1592, 1593,
            1768, 1964, 1969],
    ];
}

/** Raumtyp-Feldkandidaten (erster vorhandener gewinnt; sonst dynamische Suche). */
const RT_KEYS_SCHRANK_SL = [
    'sicherheitsschrank_saeure_lauge', 'gefahrstoffschrank_saeure_lauge',
    'schrank_saeure_lauge', 'saeure_lauge_schrank',
];
const RT_KEYS_SCHRANK_BRENNBAR = [
    'sicherheitsschrank_brennbar', 'gefahrstoffschrank_brennbar',
    'schrank_brennbar', 'brennbare_fluessigkeiten', 'brennbar',
];

/** Whitelist der per Einzel-Button setzbaren Spalten. */
const SETTABLE_COLS = ['HT_Warmwasser', 'HT_Kaltwasser', 'VE_Wasser'];

const WASCHKUECHE_TERMS = ['waschküche', 'waschkueche'];

/** Raumtyp „Waschküche“. */
const WASCHKUECHE_TYPE = '22';

/** Raumtyp „Lager Chemikalien“ mit flächenabhängiger Schrank-Soll-Berechnung. */
const CHEMIE_LAGER_TYPE = '29';

/** Ausnahme-Raum (Raumnr), der als einziger 3 Spülen haben darf. */
const SPUELE_AUSNAHME_RAUMNR = '232';

/** Max. zulässige Spülen je Achsenanzahl (AR_APs) – NICHT-Waschküchen. */
const SPUELEN_JE_ACHSEN = [
    3 => 1,
    6 => 1,
    9 => 2,
];

/** Raumtypen, die KEINE Elemente enthalten dürfen. */
const NO_ELEMENT_TYPES = ['13', '27', '28'];


/* ═══════════════════════════════════════════════════════════════════════════
   HELFER
   ═══════════════════════════════════════════════════════════════════════════ */

function raumtyp_index(): array
{
    global $labortypen;
    static $idx = null;
    if ($idx === null) {
        $idx = [];
        foreach ($labortypen as $t) {
            if (isset($t['id'])) $idx[(string)$t['id']] = $t;
        }
    }
    return $idx;
}

/** Alle im Raumtyp vorkommenden Feldnamen (zur Konfigurationshilfe). */
function raumtyp_all_keys(): array
{
    global $labortypen;
    $keys = [];
    foreach ($labortypen as $t) {
        foreach (array_keys($t) as $k) $keys[$k] = true;
    }
    ksort($keys);
    return array_keys($keys);
}

function parse_digestorenanzahl(?string $text): ?int
{
    if ($text === null || $text === '') return null;
    if (preg_match('/Digestorenanzahl\s*[:=]?\s*(\d+)/iu', $text, $m)) return (int)$m[1];
    return null;
}

/** Soll-Wert aus dem Raumtyp lesen. Rückgabe [wert(int)|null, schluessel|null]. */
function rt_soll(?array $typ, array $keys, array $dynMust, array $dynAny): array
{
    if (!$typ) return [null, null];
    foreach ($keys as $k) {
        if (array_key_exists($k, $typ) && $typ[$k] !== null && $typ[$k] !== '') {
            return [(int)$typ[$k], $k];
        }
    }
    foreach ($typ as $k => $v) {
        if ($v === null || $v === '') continue;
        $kl = mb_strtolower((string)$k);
        $mustOk = true;
        foreach ($dynMust as $needle) {
            if (strpos($kl, $needle) === false) {
                $mustOk = false;
                break;
            }
        }
        if (!$mustOk) continue;
        $anyOk = false;
        foreach ($dynAny as $needle) {
            if (strpos($kl, $needle) !== false) {
                $anyOk = true;
                break;
            }
        }
        if ($anyOk) return [(int)$v, (string)$k];
    }
    return [null, null];
}

function is_waschkueche(string $bezeichnung): bool
{
    $b = mb_strtolower($bezeichnung);
    foreach (WASCHKUECHE_TERMS as $t) if (strpos($b, $t) !== false) return true;
    return false;
}

/**
 * Flächenabhängige Schrank-Soll-Anzahl für „Lager Chemikalien“ (RT 29).
 * Gilt identisch für brennbare Flüssigkeiten UND Säure/Laugen.
 *   < 19 m² -> 1 ; < 34 m² -> 2 ; >= 34 m² -> 3.
 */
function chemie_lager_soll(?float $area): ?int
{
    if ($area === null) return null;
    if ($area < 19) return 1;
    if ($area < 34) return 2;
    return 3;
}

/**
 * Max. zulässige Spülen für einen NICHT-Waschküchen-Raum.
 * Ausnahme-Raum (231) darf 3; sonst je Achsenanzahl (3/6 -> 1, 9 -> 2).
 * Kein passender Wert -> null (Prüfung entfällt).
 */
function spuelen_max(int $achsen, string $raumnr): ?int
{
    if ($raumnr === SPUELE_AUSNAHME_RAUMNR) return 3;
    return SPUELEN_JE_ACHSEN[$achsen] ?? null;
}

/**
 * Max. zulässige Spülen für Waschküchen (RT 22).
 *   3 Achsen -> 1 ; ab 6 Achsen -> 2.
 */
function waschkueche_spuelen_max(int $achsen): int
{
    return ($achsen >= 6) ? 2 : 1;
}

/**
 * Erforderliche RDG-Anzahl (Minimum) für Waschküchen (RT 22).
 *   3/6 Achsen -> 3 ; größer -> 4.
 */
function waschkueche_rdg_min(int $achsen): int
{
    return ($achsen > 6) ? 4 : 2;
}

/**
 * Wasser-Anforderung je Raumtyp.
 * Rückgabe [reqWarm(bool), reqKalt(bool), reqVE(bool)].
 *   - Typ 23: keine Anforderung (kein KW/WW, kein VE).
 *   - Typ 25: Warm + Kalt, aber KEIN VE.
 *   - sonst : Warm + Kalt + VE.
 */
function wasser_requirements(?string $raumtyp): array
{
    $rt = (string)$raumtyp;
    if ($rt === '23') return [false, false, false];
    if ($rt === '25') return [true, true, false];
    return [true, true, true];
}

/** Schrank-Regel: zu wenig = error, mehr = warn, gleich = ok, kein Soll = na. */
function cabinet_status(?int $soll, int $ist): string
{
    if ($soll === null) return 'na';
    if ($ist < $soll) return 'error';
    if ($ist > $soll) return 'warn';
    return 'ok';
}

function status_weight(string $s): int
{
    return ['error' => 4, 'warn' => 3, 'neutral' => 2, 'na' => 1, 'ok' => 0][$s] ?? 1;
}

function status_badge(string $status, string $label): string
{
    switch ($status) {
        case 'ok':
            $cls = 'bg-success';
            break;
        case 'error':
            $cls = 'bg-danger';
            break;
        case 'warn':
            $cls = 'bg-warning text-dark';
            break;
        case 'neutral':
            $cls = 'bg-secondary';
            break;
        default:
            $cls = 'bg-light text-muted border';
            break; // na
    }
    return '<span class="badge ' . $cls . '">' . htmlspecialchars($label) . '</span>';
}

/**
 * Erzeugt Änderungsanweisungen für einen Raum aus den Prüf-Ergebnissen ($v).
 * Rückgabe: Liste von ['type' => 'add'|'remove'|'info', 'text' => string].
 * HINWEIS: Wasser wird hier bewusst NICHT berücksichtigt.
 */
function change_instructions(array $v): array
{
    $out = [];

    // 1) Digestoren
    if ($v['digStatus'] === 'error' && $v['digSoll'] !== null) {
        $d = (int)$v['digSoll'] - (int)$v['digIst'];
        if ($d > 0) {
            $out[] = ['type' => 'add', 'text' => "$d Digestor hinzufügen"];
        } elseif ($d < 0) {
            $out[] = ['type' => 'remove', 'text' => abs($d) . "× Digestor entfernen"];
        }
    }

    // 2) Schrank Säure/Lauge
    if ($v['slStatus'] === 'error') {
        $out[] = ['type' => 'add',
            'text' => ((int)$v['slSoll'] - (int)$v['slIst']) . "× Schrank Säure/Lauge hinzufügen"];
    } elseif ($v['slStatus'] === 'warn') {
        $out[] = ['type' => 'remove',
            'text' => ((int)$v['slIst'] - (int)$v['slSoll']) . "× Schrank Säure/Lauge entfernen (Ist > Soll)"];
    }

    // 3) Schrank brennbar
    if ($v['brStatus'] === 'error') {
        $out[] = ['type' => 'add',
            'text' => ((int)$v['brSoll'] - (int)$v['brIst']) . "× Schrank brennbar hinzufügen"];
    } elseif ($v['brStatus'] === 'warn') {
        $out[] = ['type' => 'remove',
            'text' => ((int)$v['brIst'] - (int)$v['brSoll']) . "× Schrank brennbar entfernen (Ist > Soll)"];
    }

    // 4) Augenduschen == Soll-Spülen
    if ($v['augenStatus'] === 'error') {
        $soll = (int)$v['spuelenSoll'];
        $d = $soll - (int)$v['augenIst'];
        if ($d > 0) {
            $out[] = ['type' => 'add',
                'text' => "$d × Augendusche hinzufügen (auf #Spülen = $soll)"];
        } elseif ($d < 0) {
            $out[] = ['type' => 'remove',
                'text' => abs($d) . "× Augendusche entfernen (auf #Spülen = $soll)"];
        }
    }

    // 4b) Spülen je Achsen (zu viele)
    if (($v['spuelenAchsenStatus'] ?? 'na') === 'error') {
        $d = (int)$v['spuele'] - (int)$v['spuelenMax'];
        if ($d > 0) {
            $note = $v['wako']
                ? "max. {$v['spuelenMax']} bei {$v['achsen']} Achsen (Waschküche)"
                : "max. {$v['spuelenMax']} bei {$v['achsen']} Achsen";
            $out[] = ['type' => 'remove', 'text' => "$d × Spüle entfernen ($note)"];
        }
    }

    // 5) Waschküche RDG
    if ($v['wakoStatus'] === 'error') {
        $need = (int)$v['rdgMin'] - (int)$v['rdgIst'];
        if ($need > 0) {
            $out[] = ['type' => 'add',
                'text' => "$need RDG hinzufügen (min. {$v['rdgMin']} bei {$v['achsen']} Achsen)"];
        }
    }

    // 7) Elemente unerwünscht (Typ 13/27/28)
    if ($v['noElemStatus'] === 'error') {
        $out[] = ['type' => 'remove',
            'text' => "Alle Elemente entfernen ({$v['totalElem']}) – Raumtyp darf keine haben"];
    }

    return $out;
}

/** Summiert je Raum die Anzahl der Elemente pro Gruppe (über ALLE Varianten). */
function element_group_counts($mysqli, array $roomIDs, array $groups): array
{
    $out = [];
    $roomIDs = array_values(array_unique(array_filter(array_map('intval', $roomIDs))));
    if (!$roomIDs) return $out;

    $eidToGroups = [];
    $allIDs = [];
    foreach ($groups as $gk => $ids) {
        foreach ($ids as $eid) {
            $eid = (int)$eid;
            $allIDs[$eid] = true;
            $eidToGroups[$eid][] = $gk;
        }
    }
    $allIDs = array_keys($allIDs);
    if (!$allIDs) return $out;

    foreach ($roomIDs as $rid) $out[$rid] = array_fill_keys(array_keys($groups), 0);

    $phR = implode(',', array_fill(0, count($roomIDs), '?'));
    $phE = implode(',', array_fill(0, count($allIDs), '?'));
    $types = str_repeat('i', count($roomIDs) + count($allIDs));

    $stmt = $mysqli->prepare(
        "SELECT TABELLE_Räume_idTABELLE_Räume AS rid,
                TABELLE_Elemente_idTABELLE_Elemente AS eid,
                SUM(Anzahl) AS n
         FROM tabelle_räume_has_tabelle_elemente
         WHERE TABELLE_Räume_idTABELLE_Räume IN ($phR)
           AND TABELLE_Elemente_idTABELLE_Elemente IN ($phE)
         GROUP BY TABELLE_Räume_idTABELLE_Räume, TABELLE_Elemente_idTABELLE_Elemente"
    );
    $stmt->bind_param($types, ...array_merge($roomIDs, $allIDs));
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $rid = (int)$r['rid'];
        $eid = (int)$r['eid'];
        $n = (int)$r['n'];
        if (!isset($out[$rid])) continue;
        foreach ($eidToGroups[$eid] ?? [] as $gk) $out[$rid][$gk] += $n;
    }
    $stmt->close();
    return $out;
}

/**
 * Zählt ALLE Elemente je Raum (Summe Anzahl über alle Element-Typen).
 * Wird für die Prüfung „Raumtyp 13/27/28 darf keine Elemente haben“ benötigt.
 */
function total_element_counts($mysqli, array $roomIDs): array
{
    $out = [];
    $roomIDs = array_values(array_unique(array_filter(array_map('intval', $roomIDs))));
    if (!$roomIDs) return $out;
    foreach ($roomIDs as $rid) $out[$rid] = 0;

    $phR = implode(',', array_fill(0, count($roomIDs), '?'));
    $types = str_repeat('i', count($roomIDs));

    $stmt = $mysqli->prepare(
        "SELECT TABELLE_Räume_idTABELLE_Räume AS rid, SUM(Anzahl) AS n
         FROM tabelle_räume_has_tabelle_elemente
         WHERE TABELLE_Räume_idTABELLE_Räume IN ($phR)
         GROUP BY TABELLE_Räume_idTABELLE_Räume"
    );
    $stmt->bind_param($types, ...$roomIDs);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $out[(int)$r['rid']] = (int)$r['n'];
    }
    $stmt->close();
    return $out;
}

/**
 * Lädt Räume eines Projekts und berechnet alle Prüfungen.
 * Rückgabe: [rowsView[], errPerCheck[]].
 */
function load_and_check(int $projectID): array
{
    $checkKeys = ['dig', 'sl', 'brennbar', 'augen', 'spuele_achsen', 'wako', 'wasser', 'noelem'];
    $errPerCheck = array_fill_keys($checkKeys, 0);
    $rowsView = [];
    if (!$projectID) return [$rowsView, $errPerCheck];

    $mysqli = utils_connect_sql();
    $sql = "SELECT idTABELLE_Räume, Raumnr, Raumbezeichnung, `Raumbereich Nutzer`,
                   `Raumtyp BH`, `Anmerkung Geräte`, AR_APs, `Nutzfläche`,
                   HT_Warmwasser, HT_Kaltwasser, VE_Wasser
            FROM tabelle_räume
            WHERE tabelle_projekte_idTABELLE_Projekte = ?
              AND (Entfallen IS NULL OR Entfallen = 0)
            ORDER BY Raumnr";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $projectID);
    $stmt->execute();
    $rooms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $groups = abgleich_element_ids();
    $counts = [];
    $totalCounts = [];
    if ($rooms) {
        $ids = array_map(fn($r) => (int)$r['idTABELLE_Räume'], $rooms);
        $counts = element_group_counts($mysqli, $ids, $groups);
        $totalCounts = total_element_counts($mysqli, $ids);
    }
    $mysqli->close();

    $idx = raumtyp_index();

    foreach ($rooms as $room) {
        $rid = (int)$room['idTABELLE_Räume'];
        $c = $counts[$rid] ?? [];
        $typ = $idx[(string)$room['Raumtyp BH']] ?? null;

        $warm = (int)($room['HT_Warmwasser'] ?? 0);
        $kalt = (int)($room['HT_Kaltwasser'] ?? 0);
        $veWasser = (int)($room['VE_Wasser'] ?? 0);
        $spuele = (int)($c['spuele'] ?? 0);         // aus ELEMENTEN

        // Fläche, Achsen & Raumart für Sonderregeln
        $flaeche = ($room['Nutzfläche'] !== null && $room['Nutzfläche'] !== '')
            ? (float)$room['Nutzfläche'] : null;
        $achsen = (int)($room['AR_APs'] ?? 0);
        $isChemieLager = ((string)$room['Raumtyp BH'] === CHEMIE_LAGER_TYPE);
        $wako = ((string)$room['Raumtyp BH'] === WASCHKUECHE_TYPE)
            || is_waschkueche((string)$room['Raumbezeichnung']);

        // 1) Digestoren
        $digIst = (int)($c['dig'] ?? 0);
        $digSoll = parse_digestorenanzahl($room['Anmerkung Geräte'] ?? null);
        $digStatus = ($digSoll === null) ? 'neutral' : (($digIst === $digSoll) ? 'ok' : 'error');

        // 2) Schrank Säure/Lauge
        $slIst = (int)($c['schrank_sl'] ?? 0);
        [$slSoll, $slKey] = rt_soll($typ, RT_KEYS_SCHRANK_SL,
            ['schrank'], ['saeure', 'säure', 'lauge']);
        if ($slKey === null) {
            [$slSoll, $slKey] = rt_soll($typ, RT_KEYS_SCHRANK_SL,
                ['gefahrstoff'], ['saeure', 'säure', 'lauge']);
        }

        // 3) Schrank brennbar
        $brIst = (int)($c['schrank_brennbar'] ?? 0);
        [$brSoll, $brKey] = rt_soll($typ, RT_KEYS_SCHRANK_BRENNBAR,
            ['schrank'], ['brennbar', 'fluessig', 'flüssig']);
        if ($brKey === null) {
            [$brSoll, $brKey] = rt_soll($typ, RT_KEYS_SCHRANK_BRENNBAR,
                ['gefahrstoff'], ['brennbar', 'fluessig', 'flüssig']);
        }

        // Sonderregel „Lager Chemikalien“ (RT 29): Soll nach Nutzfläche,
        //   identisch für Säure/Lauge und brennbare Flüssigkeiten.
        if ($isChemieLager) {
            $chemSoll = chemie_lager_soll($flaeche);
            if ($chemSoll !== null) {
                $flStr = $flaeche === null ? '?'
                    : rtrim(rtrim(number_format($flaeche, 1, ',', ''), '0'), ',');
                $slSoll = $chemSoll;
                $brSoll = $chemSoll;
                $slKey = 'RT29 · ' . $flStr . ' m²';
                $brKey = 'RT29 · ' . $flStr . ' m²';
            }
        }

        $slStatus = cabinet_status($slSoll, $slIst);
        $brStatus = cabinet_status($brSoll, $brIst);

        // 4b) Spülen je Achsen (AR_APs)
        //   Waschküche (RT 22): 3 -> 1, ab 6 -> 2.
        //   Sonst: 3/6 -> 1, 9 -> 2 (Ausnahme Raum 231 -> 3); andere Achsen -> keine Prüfung.
        if ($wako) {
            $spuelenMax = waschkueche_spuelen_max($achsen);
        } else {
            $spuelenMax = spuelen_max($achsen, (string)$room['Raumnr']);
        }
        $spuelenAchsenStatus = ($spuelenMax === null)
            ? 'na'
            : (($spuele <= $spuelenMax) ? 'ok' : 'error');

        // Soll-Spülenanzahl = nach Achsen-Korrektur verbleibende Spülen
        //   (bei Überzahl der Max-Wert, sonst der Ist-Wert).
        $spuelenSoll = ($spuelenAchsenStatus === 'error' && $spuelenMax !== null)
            ? $spuelenMax : $spuele;

        // 4) Augenduschen == Soll-Spülen (nicht Ist-Spülen)
        $augenIst = (int)($c['augendusche'] ?? 0);
        $augenStatus = ($augenIst === $spuelenSoll) ? 'ok' : 'error';

        // 5) Waschküche RDG – Anzahl je Achsen (3/6 -> 3, größer -> 4)
        $rdgIst = (int)($c['rdg'] ?? 0);
        $rdgMin = $wako ? waschkueche_rdg_min($achsen) : 0;
        $wakoStatus = !$wako ? 'na' : (($rdgIst >= $rdgMin) ? 'ok' : 'error');

        // 6) Wasser bei Spüle – raumtyp-abhängig
        [$reqWarm, $reqKalt, $reqVE] = wasser_requirements($room['Raumtyp BH']);
        $warmOk = $warm >= 1;
        $kaltOk = $kalt >= 1;
        $veOk = $veWasser >= 1;

        if ($spuele <= 0) {
            $wasserStatus = 'na';
        } elseif (!$reqWarm && !$reqKalt && !$reqVE) {
            // z.B. Typ 23 – kein Wasser erforderlich
            $wasserStatus = 'na';
        } else {
            $okWarm = !$reqWarm || $warmOk;
            $okKalt = !$reqKalt || $kaltOk;
            $okVE = !$reqVE || $veOk;
            $wasserStatus = ($okWarm && $okKalt && $okVE) ? 'ok' : 'error';
        }

        // 7) Raumtypen 13/27/28 dürfen KEINE Elemente haben
        $totalElem = (int)($totalCounts[$rid] ?? 0);
        $noElemType = in_array((string)$room['Raumtyp BH'], NO_ELEMENT_TYPES, true);
        $noElemStatus = !$noElemType ? 'na' : (($totalElem === 0) ? 'ok' : 'error');

        $statuses = ['dig' => $digStatus, 'sl' => $slStatus, 'brennbar' => $brStatus,
            'augen' => $augenStatus, 'spuele_achsen' => $spuelenAchsenStatus,
            'wako' => $wakoStatus, 'wasser' => $wasserStatus,
            'noelem' => $noElemStatus];
        $errCount = 0;
        foreach ($statuses as $k => $st) {
            if ($st === 'error') {
                $errPerCheck[$k]++;
                $errCount++;
            }
        }

        $rowsView[] = [
            'rid' => $rid, 'room' => $room, 'typ' => $typ,
            'search' => mb_strtolower($room['Raumnr'] . ' ' . $room['Raumbezeichnung']),
            'errCount' => $errCount,
            'digIst' => $digIst, 'digSoll' => $digSoll, 'digStatus' => $digStatus,
            'slIst' => $slIst, 'slSoll' => $slSoll, 'slKey' => $slKey, 'slStatus' => $slStatus,
            'brIst' => $brIst, 'brSoll' => $brSoll, 'brKey' => $brKey, 'brStatus' => $brStatus,
            'augenIst' => $augenIst, 'spuele' => $spuele, 'augenStatus' => $augenStatus,
            'achsen' => $achsen, 'spuelenMax' => $spuelenMax,
            'spuelenSoll' => $spuelenSoll, 'spuelenAchsenStatus' => $spuelenAchsenStatus,
            'flaeche' => $flaeche, 'isChemieLager' => $isChemieLager,
            'wako' => $wako, 'rdgIst' => $rdgIst, 'rdgMin' => $rdgMin, 'wakoStatus' => $wakoStatus,
            'warm' => $warm, 'kalt' => $kalt, 'veWasser' => $veWasser,
            'warmOk' => $warmOk, 'kaltOk' => $kaltOk, 'veOk' => $veOk,
            'reqWarm' => $reqWarm, 'reqKalt' => $reqKalt, 'reqVE' => $reqVE,
            'wasserStatus' => $wasserStatus,
            'totalElem' => $totalElem, 'noElemType' => $noElemType, 'noElemStatus' => $noElemStatus,
        ];
    }
    return [$rowsView, $errPerCheck];
}


/* ═══════════════════════════════════════════════════════════════════════════
   POST  action=set_value  ->  EINEN Wert für EINEN Raum setzen (JSON)
   ═══════════════════════════════════════════════════════════════════════════ */
if (($_POST['action'] ?? '') === 'set_value') {
    ob_start();
    check_login();
    header('Content-Type: application/json');

    $projectID = (int)($_POST['projectID'] ?? $_SESSION['projectID'] ?? 0);
    $roomID = (int)($_POST['roomID'] ?? 0);
    $col = (string)($_POST['col'] ?? '');
    $val = (int)($_POST['value'] ?? 1);

    if (!$projectID || !$roomID || !in_array($col, SETTABLE_COLS, true)) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter.']);
        exit;
    }

    $mysqli = utils_connect_sql();
    // Spaltenname stammt ausschließlich aus der Whitelist -> sicher.
    $sql = "UPDATE tabelle_räume SET `$col` = ?
            WHERE idTABELLE_Räume = ? AND tabelle_projekte_idTABELLE_Projekte = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('iii', $val, $roomID, $projectID);
    $stmt->execute();
    $ok = $stmt->affected_rows;
    $stmt->close();
    $mysqli->close();

    ob_end_clean();
    echo json_encode(['status' => 'ok', 'updated' => $ok,
        'msg' => "$col = $val gesetzt (Raum $roomID)."]);
    exit;
}


/* ═══════════════════════════════════════════════════════════════════════════
   GET  action=export  ->  CSV-Download
   ═══════════════════════════════════════════════════════════════════════════ */
if (($_GET['action'] ?? '') === 'export') {
    check_login();
    $projectID = (int)($_GET['projectID'] ?? $_SESSION['projectID'] ?? 0);
    [$rowsView] = load_and_check($projectID);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="RaumAbgleich_P' . $projectID . '.csv"');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM (Excel)
    $out = fopen('php://output', 'w');
    fputcsv($out, [
        'Raumnr', 'Bezeichnung', 'Bereich', 'RaumtypID', 'Nutzflaeche', 'Achsen', 'Fehleranzahl',
        'Dig_Ist', 'Dig_Soll', 'Dig_Status',
        'SL_Ist', 'SL_Soll', 'SL_Status',
        'Brennbar_Ist', 'Brennbar_Soll', 'Brennbar_Status',
        'Augenduschen', 'Spuelen', 'Augen=Spuele_Status',
        'Spuelen_max', 'Spuelen_Achsen_Status',
        'Waschkueche', 'RDG_Ist', 'RDG_min', 'Waschkueche_Status',
        'Warmwasser', 'Kaltwasser', 'VE_Wasser', 'Wasser_Status',
        'Elemente_gesamt', 'NoElem_Typ', 'NoElem_Status',
        'Aenderungsanweisungen',
    ], ';');
    foreach ($rowsView as $v) {
        $r = $v['room'];
        $instrTexts = array_map(fn($i) => $i['text'], change_instructions($v));
        fputcsv($out, [
            $r['Raumnr'], $r['Raumbezeichnung'], $r['Raumbereich Nutzer'],
            $r['Raumtyp BH'], $v['flaeche'] ?? '', $v['achsen'], $v['errCount'],
            $v['digIst'], $v['digSoll'] ?? '', $v['digStatus'],
            $v['slIst'], $v['slSoll'] ?? '', $v['slStatus'],
            $v['brIst'], $v['brSoll'] ?? '', $v['brStatus'],
            $v['augenIst'], $v['spuele'], $v['augenStatus'],
            $v['spuelenMax'] ?? '', $v['spuelenAchsenStatus'],
            $v['wako'] ? 'ja' : 'nein', $v['rdgIst'], $v['rdgMin'], $v['wakoStatus'],
            $v['warm'], $v['kalt'], $v['veWasser'], $v['wasserStatus'],
            $v['totalElem'], $v['noElemType'] ? 'ja' : 'nein', $v['noElemStatus'],
            implode(' | ', $instrTexts),
        ], ';');
    }
    fclose($out);
    exit;
}


/* ═══════════════════════════════════════════════════════════════════════════
   GET  ->  Seite rendern
   ═══════════════════════════════════════════════════════════════════════════ */
init_page_serversides();

$projectID = (int)($_GET['projectID'] ?? $_SESSION['projectID'] ?? 0);
[$rowsView, $errPerCheck] = load_and_check($projectID);
$totalErrors = array_sum($errPerCheck);
$rtKeys = raumtyp_all_keys();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Raum-Abgleich (Prüfungen)</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="../Logo/iphone_favicon.png"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>
<body>
<div class="container-fluid bg-light py-3">
    <div id="limet-navbar"></div>
    <div class="card mt-1">
        <div class="card-header">

            <div class="d-flex flex-nowrap align-items-center gap-3 overflow-auto col-12">

                <a class="btn btn-sm btn-outline-success flex-shrink-0"
                   href="?action=export&projectID=<?= (int)$projectID ?>">
                    <i class="fas fa-file-csv me-1"></i>
                    <b>
                        <i class="fas fa-clipboard-check me-1"></i>
                        Raum-Abgleich – Prüfungen
                    </b>
                </a>


                <!-- Dokumentation / Konfiguration -->
                <div class="accordion flex-shrink-0" id="docAcc">
                    <div class="accordion-item">

                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#docBody">
                                <i class="fas fa-info-circle me-2"></i>
                                Festlegungen &amp; Prüfungen &amp; Konfiguration
                            </button>
                        </h2>

                        <div id="docBody"
                             class="accordion-collapse collapse"
                             data-bs-parent="#docAcc">

                            <div class="accordion-body">

                                <ol>
                                    <li>
                                        <b>Digestoren:</b>
                                        Elemente == „Digestorenanzahl X“ aus RUF (=<code>Anmerkung Geräte</code>).
                                    </li>
                                    <li>
                                        <b>Sicherheitsschrank Säure/Lauge</b> (1092) und
                                        <b>Sicherheitsschrank brennbare</b>
                                        (1093) gegen Soll laut Raumtyp.
                                        Regel:
                                        <span class="badge bg-danger">Ist &lt; Soll</span>
                                        <span class="badge bg-warning text-dark">Ist &gt; Soll</span>
                                        <span class="badge bg-success">Ist = Soll</span>.
                                    </li>
                                    <li>
                                        <b>Lager Chemikalien (Raumtyp 29):</b>
                                        Schrank-Soll nach <code>Nutzfläche</code> –
                                        &lt; 19 m² &rarr; 1×, &lt; 34 m² &rarr; 2×, &ge; 34 m² &rarr; 3×
                                        (jeweils brennbar <u>und</u> Säure/Lauge).
                                    </li>
                                    <li>
                                        <b>#Augenduschen = #Spülen:</b>
                                        Beide Werte müssen aus den Elementen übereinstimmen.
                                    </li>
                                    <li>
                                        <b>Spülen je Achsen (<code>AR_APs</code>):</b>
                                        3/6 Achsen &rarr; max. 1 Spüle, 9 Achsen &rarr; max. 2.
                                        Ausnahme: Raum <code><?= htmlspecialchars(SPUELE_AUSNAHME_RAUMNR) ?></code>
                                        darf 3 Spülen haben. Gilt <u>nicht</u> für Waschküchen.
                                    </li>
                                    <li>
                                        <b>Waschküche (Raumtyp 22):</b>
                                        Spülen &ndash; 3 Achsen &rarr; 1, ab 6 Achsen &rarr; 2.
                                        RDG &ndash; 3/6 Achsen &rarr; 2, größer &rarr; 4.
                                    </li>
                                    <li>
                                        <b>Wasser:</b>
                                        Bei Spülen müssen Warm-, Kalt- und VE-Wasser gesetzt sein.
                                    </li>
                                    <li>
                                        <b>Wasser – Sonderregeln je Raumtyp:</b>
                                        Typ <code>25</code> benötigt Warm- und Kaltwasser, aber
                                        <u>kein</u> VE-Wasser. Typ <code>23</code> benötigt
                                        <u>weder</u> KW/WW <u>noch</u> VE-Wasser (Prüfung entfällt).
                                    </li>
                                    <li>
                                        <b>Elemente unerwünscht:</b>
                                        Raumtypen <code>13</code>, <code>27</code> und <code>28</code>
                                        dürfen <u>keine</u> Elemente enthalten.
                                    </li>
                                    <li>
                                        <b>Änderungsanweisungen:</b>
                                        Letzte Spalte leitet konkrete Handlungstexte ab
                                        (<i class="fas fa-plus-circle text-success"></i> hinzufügen /
                                        <i class="fas fa-minus-circle text-danger"></i> entfernen).
                                        Wasser wird dort NICHT berücksichtigt.
                                    </li>
                                </ol>
                                <hr>

                            </div>
                        </div>

                    </div>
                </div>


                <!-- Zusammenfassung -->
                <div class="d-flex flex-nowrap align-items-center gap-2 flex-shrink-0 text-nowrap justify-content-end">
                    <span class="badge bg-secondary">
                        Räume: <?= count($rowsView) ?>
                    </span>
                    <span class="badge <?= $totalErrors ? 'bg-danger' : 'bg-success' ?>">
                        Fehler gesamt: <?= $totalErrors ?>
                    </span>
                    <span class="badge <?= $errPerCheck['dig'] ? 'bg-danger' : 'bg-light text-muted border' ?>">
                        Digestoren: <?= $errPerCheck['dig'] ?>
                    </span>
                    <span class="badge <?= $errPerCheck['sl'] ? 'bg-danger' : 'bg-light text-muted border' ?>">
                        Schrank S/L: <?= $errPerCheck['sl'] ?>
                    </span>
                    <span class="badge <?= $errPerCheck['brennbar'] ? 'bg-danger' : 'bg-light text-muted border' ?>">
                        Schrank brennbar: <?= $errPerCheck['brennbar'] ?>
                    </span>
                    <span class="badge <?= $errPerCheck['augen'] ? 'bg-danger' : 'bg-light text-muted border' ?>">
                        Augend. = Spüle: <?= $errPerCheck['augen'] ?>
                    </span>
                    <span class="badge <?= $errPerCheck['spuele_achsen'] ? 'bg-danger' : 'bg-light text-muted border' ?>">
                        Spülen/Achsen: <?= $errPerCheck['spuele_achsen'] ?>
                    </span>
                    <span class="badge <?= $errPerCheck['wako'] ? 'bg-danger' : 'bg-light text-muted border' ?>">
                        Waschküche RDG: <?= $errPerCheck['wako'] ?>
                    </span>
                    <span class="badge <?= $errPerCheck['wasser'] ? 'bg-danger' : 'bg-light text-muted border' ?>">
                        Wasser W/K/VE: <?= $errPerCheck['wasser'] ?>
                    </span>
                    <span class="badge <?= $errPerCheck['noelem'] ? 'bg-danger' : 'bg-light text-muted border' ?>">
                        Elemente 13/27/28: <?= $errPerCheck['noelem'] ?>
                    </span>
                </div>


                <!-- Filter -->
                <div class="d-flex flex-nowrap align-items-center gap-2 flex-shrink-0">
                    <input type="text"
                           id="roomFilter"
                           class="form-control form-control-sm w-auto"
                           placeholder="Raum filtern (Nr / Bezeichnung)…">
                    <div class="form-check form-check-inline mb-0 text-nowrap">
                        <input type="checkbox"
                               class="form-check-input"
                               id="onlyErrors">

                        <label class="form-check-label small"
                               for="onlyErrors">
                            nur Fehler zeigen
                        </label>
                    </div>
                </div>

            </div>

        </div>


        <div class="card-body">
            <?php if (!$projectID): ?>
                <p class="text-muted fst-italic">Bitte eine Projekt-ID angeben.</p>
            <?php elseif (empty($rowsView)): ?>
                <p class="text-muted fst-italic">Keine (aktiven) Räume in Projekt <?= $projectID ?> gefunden.</p>
            <?php else: ?>


                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle" id="tbl">
                        <thead class="table-light">
                        <tr>
                            <?php
                            $cols = ['Fehler', 'Raumnr', 'Bezeichnung', 'Raumtyp',
                                'Digestoren', 'Schrank S/L', 'Schrank brennbar',
                                'Augend. = Spüle', 'Spülen/Achsen', 'Waschküche RDG', 'Wasser W/K/VE',
                                'Elemente 13/27/28', 'Änderungsanweisungen'];
                            foreach ($cols as $i => $label): ?>
                                <th class="text-nowrap">
                                    <button type="button"
                                            class="btn btn-sm btn-link text-reset text-decoration-none p-0 fw-bold sort-btn"
                                            data-col="<?= $i ?>">
                                        <?= htmlspecialchars($label) ?> <i class="fas fa-sort text-muted"></i>
                                    </button>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rowsView as $v):
                            $room = $v['room'];
                            $hasError = $v['errCount'] > 0;
                            $instr = change_instructions($v);
                            ?>
                            <tr class="room-row" data-search="<?= htmlspecialchars($v['search']) ?>"
                                data-error="<?= $hasError ? '1' : '0' ?>">

                                <!-- 0: Fehleranzahl -->
                                <td data-sort="<?= $v['errCount'] ?>">
                                    <?php if ($v['errCount']): ?>
                                        <span class="badge bg-danger"><?= $v['errCount'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success">0</span>
                                    <?php endif; ?>
                                </td>

                                <!-- 1: Raumnr -->
                                <td class="text-nowrap" data-sort="<?= htmlspecialchars($room['Raumnr']) ?>">
                                    <?= htmlspecialchars($room['Raumnr']) ?>
                                </td>

                                <!-- 2: Bezeichnung -->
                                <td data-sort="<?= htmlspecialchars(mb_strtolower($room['Raumbezeichnung'])) ?>">
                                    <?= htmlspecialchars($room['Raumbezeichnung']) ?>
                                    <?php if (!empty($room['Raumbereich Nutzer'])): ?>
                                        <span class="text-muted small d-block"><?= htmlspecialchars($room['Raumbereich Nutzer']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($v['wako']): ?>
                                        <span class="badge bg-info text-dark mt-1">Waschküche</span>
                                    <?php endif; ?>
                                    <?php if ($v['isChemieLager']): ?>
                                        <span class="badge bg-info text-dark mt-1">Lager Chemikalien</span>
                                    <?php endif; ?>
                                </td>

                                <!-- 3: Raumtyp -->
                                <td class="text-nowrap"
                                    data-sort="<?= htmlspecialchars((string)$room['Raumtyp BH']) ?>">
                                    <?php if ($v['typ']): ?>
                                        <span class="badge bg-secondary">#<?= htmlspecialchars((string)$room['Raumtyp BH']) ?></span>
                                    <?php elseif ($room['Raumtyp BH'] !== null && $room['Raumtyp BH'] !== ''): ?>
                                        <span class="badge bg-warning text-dark">#<?= htmlspecialchars((string)$room['Raumtyp BH']) ?> ?</span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                    <?php if ($v['achsen']): ?>
                                        <span class="text-muted small d-block"><?= (int)$v['achsen'] ?> Achsen</span>
                                    <?php endif; ?>
                                </td>

                                <!-- 4: Digestoren -->
                                <td data-sort="<?= status_weight($v['digStatus']) ?>">
                                    <?= status_badge($v['digStatus'],
                                        $v['digStatus'] === 'neutral' ? 'k.A.' : ($v['digStatus'] === 'na' ? 'n.k.' : strtoupper($v['digStatus']))) ?>
                                    <div class="small text-muted">Ist <b><?= $v['digIst'] ?></b> /
                                        Soll <?= $v['digSoll'] === null ? '—' : '<b>' . $v['digSoll'] . '</b>' ?></div>
                                </td>

                                <!-- 5: Schrank S/L -->
                                <td data-sort="<?= status_weight($v['slStatus']) ?>">
                                    <?php
                                    $slLbl = ['ok' => 'OK', 'error' => 'zu wenig', 'warn' => 'mehr', 'na' => 'n.k.'][$v['slStatus']] ?? '';
                                    echo status_badge($v['slStatus'], $slLbl);
                                    ?>
                                    <div class="small text-muted">Ist <b><?= $v['slIst'] ?></b> /
                                        Soll <?= $v['slSoll'] === null ? '—' : '<b>' . $v['slSoll'] . '</b>' ?>
                                        <?php if ($v['slKey']): ?><span class="d-block"><i
                                                class="fas fa-tag"></i> <?= htmlspecialchars($v['slKey']) ?>
                                            </span><?php endif; ?>
                                    </div>
                                </td>

                                <!-- 6: Schrank brennbar -->
                                <td data-sort="<?= status_weight($v['brStatus']) ?>">
                                    <?php
                                    $brLbl = ['ok' => 'OK', 'error' => 'zu wenig', 'warn' => 'mehr', 'na' => 'n.k.'][$v['brStatus']] ?? '';
                                    echo status_badge($v['brStatus'], $brLbl);
                                    ?>
                                    <div class="small text-muted">Ist <b><?= $v['brIst'] ?></b> /
                                        Soll <?= $v['brSoll'] === null ? '—' : '<b>' . $v['brSoll'] . '</b>' ?>
                                        <?php if ($v['brKey']): ?><span class="d-block"><i
                                                class="fas fa-tag"></i> <?= htmlspecialchars($v['brKey']) ?>
                                            </span><?php endif; ?>
                                    </div>
                                </td>

                                <!-- 7: Augend. = Spüle -->
                                <td data-sort="<?= status_weight($v['augenStatus']) ?>">
                                    <?= status_badge($v['augenStatus'], strtoupper($v['augenStatus'])) ?>
                                    <div class="small text-muted">Augend. <b><?= $v['augenIst'] ?></b> / Spülen-Soll
                                        <b><?= $v['spuelenSoll'] ?></b>
                                        <?php if ($v['spuelenSoll'] !== $v['spuele']): ?>
                                            <span class="d-block">Ist-Spülen <?= $v['spuele'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- 8: Spülen/Achsen -->
                                <td data-sort="<?= status_weight($v['spuelenAchsenStatus']) ?>">
                                    <?php if ($v['spuelenMax'] !== null): ?>
                                        <?php
                                        $saLbl = ['ok' => 'OK', 'error' => 'zu viele', 'na' => 'n.k.'][$v['spuelenAchsenStatus']] ?? strtoupper($v['spuelenAchsenStatus']);
                                        echo status_badge($v['spuelenAchsenStatus'], $saLbl);
                                        ?>
                                        <div class="small text-muted">
                                            Spülen <b><?= $v['spuele'] ?></b> / max <b><?= $v['spuelenMax'] ?></b>
                                            <span class="d-block"><?= (int)$v['achsen'] ?> Achsen<?= $v['wako'] ? ' · Waschküche' : '' ?></span>
                                        </div>
                                        <?php if ((string)$room['Raumnr'] === SPUELE_AUSNAHME_RAUMNR): ?>
                                            <div class="small text-info">Ausnahme: bis 3 Spülen</div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">—
                                            <?php if ($v['achsen']): ?><span class="d-block"><?= (int)$v['achsen'] ?>
                                                Achsen</span><?php endif; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- 9: Waschküche RDG -->
                                <td data-sort="<?= status_weight($v['wakoStatus']) ?>">
                                    <?php if ($v['wako']): ?>
                                        <?= status_badge($v['wakoStatus'], strtoupper($v['wakoStatus'])) ?>
                                        <div class="small text-muted">RDG <b><?= $v['rdgIst'] ?></b> /
                                            min. <b><?= $v['rdgMin'] ?></b>
                                            <span class="d-block"><?= (int)$v['achsen'] ?> Achsen</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>

                                <!-- 10: Wasser W/K/VE -->
                                <td data-sort="<?= status_weight($v['wasserStatus']) ?>">
                                    <?php if ($v['spuele'] > 0): ?>
                                        <?php
                                        $wasserLbl = ['ok' => 'OK', 'error' => 'FEHLT', 'na' => 'n.k.'][$v['wasserStatus']] ?? strtoupper($v['wasserStatus']);
                                        echo status_badge($v['wasserStatus'], $wasserLbl);
                                        ?>
                                        <div class="small mt-1">
                                            <?php /* Warmwasser */ ?>
                                            <?php if ($v['reqWarm']): ?>
                                                <span class="<?= $v['warmOk'] ? 'text-muted' : 'text-danger fw-bold' ?>">W&nbsp;<?= $v['warm'] ?></span>
                                                <?php if (!$v['warmOk']): ?>
                                                    <button class="btn btn-outline-danger btn-sm py-0 px-1 set-val"
                                                            data-room="<?= $v['rid'] ?>" data-col="HT_Warmwasser">W→1
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">W&nbsp;n.k.</span>
                                            <?php endif; ?>
                                            &nbsp;
                                            <?php /* Kaltwasser */ ?>
                                            <?php if ($v['reqKalt']): ?>
                                                <span class="<?= $v['kaltOk'] ? 'text-muted' : 'text-danger fw-bold' ?>">K&nbsp;<?= $v['kalt'] ?></span>
                                                <?php if (!$v['kaltOk']): ?>
                                                    <button class="btn btn-outline-danger btn-sm py-0 px-1 set-val"
                                                            data-room="<?= $v['rid'] ?>" data-col="HT_Kaltwasser">K→1
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">K&nbsp;n.k.</span>
                                            <?php endif; ?>
                                            &nbsp;
                                            <?php /* VE-Wasser */ ?>
                                            <?php if ($v['reqVE']): ?>
                                                <span class="<?= $v['veOk'] ? 'text-muted' : 'text-danger fw-bold' ?>">VE&nbsp;<?= $v['veOk'] ? 'ja' : 'nein' ?></span>
                                                <?php if (!$v['veOk']): ?>
                                                    <button class="btn btn-outline-danger btn-sm py-0 px-1 set-val"
                                                            data-room="<?= $v['rid'] ?>" data-col="VE_Wasser">VE→ja
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">VE&nbsp;n.k.</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                        $rtStr = (string)$room['Raumtyp BH'];
                                        if ($rtStr === '25'): ?>
                                            <div class="small text-info">Typ 25: ohne VE</div>
                                        <?php elseif ($rtStr === '23'): ?>
                                            <div class="small text-info">Typ 23: kein Wasser</div>
                                        <?php endif; ?>
                                        <div class="small text-muted">Spülen <?= $v['spuele'] ?></div>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>

                                <!-- 11: Elemente 13/27/28 (dürfen keine haben) -->
                                <td data-sort="<?= status_weight($v['noElemStatus']) ?>">
                                    <?php if ($v['noElemType']): ?>
                                        <?= status_badge($v['noElemStatus'],
                                            $v['noElemStatus'] === 'ok' ? 'OK' : 'hat Elemente') ?>
                                        <div class="small text-muted">Elemente <b><?= $v['totalElem'] ?></b> /
                                            erwartet <b>0</b></div>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>

                                <!-- 12: Änderungsanweisungen -->
                                <td data-sort="<?= count($instr) ?>" class="text-nowrap">
                                    <?php if ($instr): ?>
                                        <?php foreach ($instr as $ins):
                                            $ic = ['add' => 'fa-plus-circle text-success',
                                                'remove' => 'fa-minus-circle text-danger',
                                                'info' => 'fa-info-circle text-primary'][$ins['type']] ?? 'fa-circle text-muted';
                                            ?>
                                            <div class="small">
                                                <i class="fas <?= $ic ?> me-1"></i><?= htmlspecialchars($ins['text']) ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<script src="utils/_utils.js"></script>
<script>
    const PROJECT_ID = <?= (int)$projectID ?>;
    const SELF = location.pathname.split('/').pop();

    $(function () {

        /* ---- Filter ---- */
        function applyFilter() {
            const q = ($('#roomFilter').val() || '').toLowerCase().trim();
            const onlyErr = $('#onlyErrors').is(':checked');
            $('#tbl tbody tr.room-row').each(function () {
                const okText = !q || String($(this).data('search')).includes(q);
                const okErr = !onlyErr || String($(this).data('error')) === '1';
                $(this).toggle(okText && okErr);
            });
        }

        $('#roomFilter').on('input', applyFilter);
        $('#onlyErrors').on('change', applyFilter);

        /* ---- Sortierung ---- */
        let sortCol = 0, sortDir = -1; // Start: Fehler absteigend
        function doSort(col) {
            const tbody = $('#tbl tbody');
            const rows = tbody.find('tr.room-row').get();
            rows.sort(function (a, b) {
                const av = $(a).children('td').eq(col).attr('data-sort');
                const bv = $(b).children('td').eq(col).attr('data-sort');
                const an = parseFloat(av), bn = parseFloat(bv);
                let cmp;
                if (!isNaN(an) && !isNaN(bn) && String(an) === av.trim() && String(bn) === bv.trim()) {
                    cmp = an - bn;
                } else if (!isNaN(an) && !isNaN(bn)) {
                    cmp = an - bn;
                } else {
                    cmp = String(av).localeCompare(String(bv), 'de', {numeric: true});
                }
                return cmp * sortDir;
            });
            tbody.append(rows);
            $('.sort-btn i').attr('class', 'fas fa-sort text-muted');
            $('.sort-btn[data-col="' + col + '"] i')
                .attr('class', 'fas ' + (sortDir === 1 ? 'fa-sort-up' : 'fa-sort-down'));
        }

        $('.sort-btn').on('click', function () {
            const col = parseInt($(this).data('col'), 10);
            if (col === sortCol) sortDir *= -1; else {
                sortCol = col;
                sortDir = 1;
            }
            doSort(col);
        });
        doSort(0); // initial: Fehler zuerst

        /* ---- Einzelwert setzen ---- */
        $(document).on('click', '.set-val', function () {
            const btn = this;
            const roomID = $(btn).data('room');
            const col = $(btn).data('col');
            if (!confirm(col + ' = 1 für Raum-ID ' + roomID + ' setzen?')) return;
            btn.disabled = true;
            $.ajax({
                url: SELF, type: 'POST',
                data: {action: 'set_value', projectID: PROJECT_ID, roomID: roomID, col: col, value: 1},
                success: function (raw) {
                    let res;
                    try {
                        res = typeof raw === 'string' ? JSON.parse(raw) : raw;
                    } catch (e) {
                        res = {status: 'error', msg: String(raw)};
                    }
                    if (res.status === 'ok') {
                        makeToaster(res.msg || 'Gesetzt.', true);
                        setTimeout(() => location.reload(), 600);
                    } else {
                        makeToaster('Fehler: ' + (res.msg || ''), false);
                        btn.disabled = false;
                    }
                },
                error: function () {
                    makeToaster('Verbindungsfehler.', false);
                    btn.disabled = false;
                }
            });
        });
    });
</script>
</body>
</html>