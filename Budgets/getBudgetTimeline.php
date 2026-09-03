<?php
/**
 * getBudgetTimeline.php
 * ---------------------------------------------------------------------------
 * Liefert die Datenbasis für die Budget-Timeline & -Übersicht als JSON.
 *
 * Grundidee
 *   Jede Zeile in tabelle_rb_aenderung ist ein VOLLSTÄNDIGER Vorher/Nachher-
 *   Snapshot einer Zeile aus tabelle_räume_has_tabelle_elemente (rhe).
 *   Konvention (bestätigt):
 *       - Basisspalte           = ALT (vorher)
 *       - Spalte + "_copy1"      = NEU (nachher)
 *       - *_alt / *_neu          = vorher / nachher
 *
 *   Jede Änderung ist eine Wertbewegung:
 *       budget_alt  -=  Anzahl_alt * Kosten(el_alt, var_alt)
 *       budget_neu  +=  Anzahl_neu * Kosten(el_neu, var_neu)
 *   (nur Standort = 1 zählt - konsistent mit dem bestehenden Kosten-PDF)
 *
 *   Daraus entstehen in einem Durchlauf:
 *       - Bestandskurve je Budget über die Zeit (Graph)
 *       - Änderungsvolumen je Budget im Zeitraum (Tabelle)
 *       - Detailliste aller wertwirksamen Änderungen
 *
 * Preisbasis: AKTUELLE Kosten (tabelle_projekt_varianten_kosten).
 * ---------------------------------------------------------------------------
 */

ob_start(); // init_page_serversides() kann HTML ausgeben -> verwerfen
require_once "../utils/_utils.php";
init_page_serversides();

$mysqli = utils_connect_sql();
$mysqli->set_charset('utf8mb4');

/* ---------- Eingaben -------------------------------------------------- */
$dateFrom = isset($_POST['dateFrom']) ? $_POST['dateFrom'] : date('Y-m-d', strtotime('-1 year'));
$dateTo = isset($_POST['dateTo']) ? $_POST['dateTo'] : date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) $dateFrom = date('Y-m-d', strtotime('-1 year'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) $dateTo = date('Y-m-d');

// Standort-Filter: standardmäßig nur Standort = 1 (wie im Kosten-PDF)
$onlyStandort1 = !isset($_POST['allStandorte']) || $_POST['allStandorte'] !== '1';

$fromStartUnix = strtotime($dateFrom . ' 00:00:00');
$toEndUnix = strtotime($dateTo . ' 23:59:59');

$projectID = isset($_SESSION["projectID"]) ? (int)$_SESSION["projectID"] : 0;

/* ---------- Helfer ---------------------------------------------------- */
function parseMoney($s)
{
    if ($s === null) return 0.0;
    $s = trim((string)$s);
    if ($s === '') return 0.0;
    $s = preg_replace('/[^0-9,.\-]/', '', $s);
    if ($s === '' || $s === '-') return 0.0;
    $hasComma = strpos($s, ',') !== false;
    $hasDot = strpos($s, '.') !== false;
    if ($hasComma && $hasDot) {          // 1.234,56  -> Punkt = Tausender
        $s = str_replace('.', '', $s);
        $s = str_replace(',', '.', $s);
    } elseif ($hasComma) {               // 1234,56
        $s = str_replace(',', '.', $s);
    }
    return (float)$s;
}

try {
    /* ---------- Budgets ---------------------------------------------- */
    $budgets = [0 => 'Ohne Budget'];
    $stmt = $mysqli->prepare(
        "SELECT idtabelle_projektbudgets, Budgetnummer, Budgetname
           FROM tabelle_projektbudgets
          WHERE tabelle_projekte_idTABELLE_Projekte = ?
          ORDER BY Budgetnummer"
    );
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $budgets[(int)$r['idtabelle_projektbudgets']] =
            trim($r['Budgetnummer'] . ' - ' . $r['Budgetname']);
    }
    $stmt->close();

    /* ---------- Preis-Map (aktuelle Kosten) -------------------------- */
    // Schlüssel "element:variante" -> Kosten (float). Join wie im Kosten-PDF
    // über Projekt+Element+Variante (Gerät bleibt unberücksichtigt).
    $price = [];
    $stmt = $mysqli->prepare(
        "SELECT tabelle_elemente_idTABELLE_Elemente AS el,
                tabelle_Varianten_idtabelle_Varianten AS var,
                Kosten
           FROM tabelle_projekt_varianten_kosten
          WHERE tabelle_projekte_idTABELLE_Projekte = ?"
    );
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $k = $r['el'] . ':' . $r['var'];
        $v = parseMoney($r['Kosten']);
        if (!isset($price[$k]) || $v != 0.0) $price[$k] = $v; // nicht-leeren Wert bevorzugen
    }
    $stmt->close();

    /* ---------- Varianten-Namen -------------------------------------- */
    $varName = [];
    $res = $mysqli->query("SELECT idtabelle_Varianten, Variante FROM tabelle_varianten");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $varName[(int)$r['idtabelle_Varianten']] = $r['Variante'];
        }
    }

    /* ---------- Live-Zustand der rhe-Zeilen -------------------------- */
    // Für Zeilen, die NIE geändert wurden (kein Log-Eintrag) -> Baseline.
    $live = [];
    $stmt = $mysqli->prepare(
        "SELECT rhe.id,
                rhe.Anzahl,
                rhe.Standort,
                rhe.tabelle_Varianten_idtabelle_Varianten          AS var,
                rhe.tabelle_projektbudgets_idtabelle_projektbudgets AS bud,
                rhe.TABELLE_Elemente_idTABELLE_Elemente             AS el
           FROM tabelle_räume_has_tabelle_elemente rhe
           INNER JOIN tabelle_räume r
                   ON r.idTABELLE_Räume = rhe.TABELLE_Räume_idTABELLE_Räume
          WHERE r.tabelle_projekte_idTABELLE_Projekte = ?"
    );
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $live[(int)$r['id']] = $r;
    }
    $stmt->close();

    /* ---------- Contribution-Helfer ---------------------------------- */
    $contrib = function ($anzahl, $standort, $el, $var) use ($price, $onlyStandort1) {
        if ($anzahl === null) return 0.0;
        $a = (int)$anzahl;
        if ($a <= 0) return 0.0;
        if ($onlyStandort1 && (int)$standort !== 1) return 0.0;
        $p = $price[$el . ':' . $var] ?? 0.0;
        return $a * $p;
    };

    /* ---------- Alle Änderungen des Projekts laden ------------------- */
    // Raum/Element per COALESCE(neu, alt) -> erfasst auch Löschzeilen.
    $sql = "
        SELECT
            a.id                                            AS rowid,
            a.Timestamp,
            a.user,
            a.Anzahl                                        AS anz_alt,
            a.Anzahl_copy1                                  AS anz_neu,
            a.Standort                                      AS so_alt,
            a.Standort_copy1                                AS so_neu,
            a.tabelle_Varianten_idtabelle_Varianten         AS var_alt,
            a.tabelle_Varianten_idtabelle_Varianten_copy1   AS var_neu,
            a.elementID_alt, a.elementID_neu,
            a.projektBudgetID_alt                           AS bud_alt,
            a.projektBudgetID_neu                           AS bud_neu,
            el.Bezeichnung                                  AS element_name,
            r.Raumnr                                        AS raumnr,
            r.Raumbezeichnung                               AS raumname
        FROM tabelle_rb_aenderung a
        LEFT JOIN tabelle_räume r
               ON r.idTABELLE_Räume = COALESCE(a.raumID_neu, a.raumID_alt)
        LEFT JOIN tabelle_elemente el
               ON el.idTABELLE_Elemente = COALESCE(a.elementID_neu, a.elementID_alt)
        WHERE r.tabelle_projekte_idTABELLE_Projekte = ?
        ORDER BY a.Timestamp ASC, a.idtabelle_rb_aenderung ASC
    ";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) throw new Exception("Prepare failed: " . $mysqli->error);
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    $res = $stmt->get_result();

    // Events vorbereiten + je rhe-id gruppieren
    $events = [];   // chronologische Liste
    $eventsById = [];   // id -> [indizes in $events]
    while ($r = $res->fetch_assoc()) {
        $ts = strtotime($r['Timestamp']);
        if ($ts === false) continue;

        $rowid = (int)$r['rowid'];
        $budAlt = ($r['bud_alt'] === null) ? 0 : (int)$r['bud_alt'];
        $budNeu = ($r['bud_neu'] === null) ? 0 : (int)$r['bud_neu'];
        $elAlt = $r['elementID_alt'] !== null ? (int)$r['elementID_alt'] : (int)$r['elementID_neu'];
        $elNeu = $r['elementID_neu'] !== null ? (int)$r['elementID_neu'] : (int)$r['elementID_alt'];

        $oldC = $contrib($r['anz_alt'], $r['so_alt'], $elAlt, (int)$r['var_alt']);
        $newC = $contrib($r['anz_neu'], $r['so_neu'], $elNeu, (int)$r['var_neu']);

        $ev = [
            'rowid' => $rowid,
            'ts' => $ts,
            'tsLabel' => date('Y-m-d H:i', $ts),
            'user' => $r['user'],
            'budAlt' => $budAlt,
            'budNeu' => $budNeu,
            'oldC' => $oldC,
            'newC' => $newC,
            'anzAlt' => $r['anz_alt'],
            'anzNeu' => $r['anz_neu'],
            'varAlt' => (int)$r['var_alt'],
            'varNeu' => (int)$r['var_neu'],
            'elName' => $r['element_name'],
            'raum' => trim(($r['raumnr'] ?? '') . ' ' . ($r['raumname'] ?? '')),
        ];
        $idx = count($events);
        $events[] = $ev;
        $eventsById[$rowid][] = $idx;
    }
    $stmt->close();

    /* ---------- Baseline: Zustand JE Zeile zum Fensterstart ---------- */
    // Pro rhe-id direkt auswerten (vermeidet Vorzeichenfehler bei Zeilen,
    // die vor Beginn des Loggings existierten).
    $startStock = [];
    foreach ($budgets as $bid => $lbl) $startStock[$bid] = 0.0;

    $seen = [];
    foreach ($eventsById as $rowid => $idxs) {
        $seen[$rowid] = true;
        // letztes Event VOR Fensterstart
        $lastBefore = null;
        foreach ($idxs as $ix) {
            if ($events[$ix]['ts'] < $fromStartUnix) $lastBefore = $ix; else break;
        }
        if ($lastBefore !== null) {
            $e = $events[$lastBefore];              // NEU-Seite = Zustand danach
            $startStock[$e['budNeu']] = ($startStock[$e['budNeu']] ?? 0) + $e['newC'];
        } else {
            $e = $events[$idxs[0]];                 // vor erster Änderung = ALT-Seite
            $startStock[$e['budAlt']] = ($startStock[$e['budAlt']] ?? 0) + $e['oldC'];
        }
    }
    // Zeilen ohne jegliche Änderung -> Live-Zustand (Annahme: durchgehend vorhanden)
    foreach ($live as $rowid => $lv) {
        if (isset($seen[$rowid])) continue;
        $bud = ($lv['bud'] === null) ? 0 : (int)$lv['bud'];
        $c = $contrib($lv['Anzahl'], $lv['Standort'], (int)$lv['el'], (int)$lv['var']);
        $startStock[$bud] = ($startStock[$bud] ?? 0) + $c;
    }

    /* ---------- Fenster-Events -> Timeline, Fluss, Detail ------------ */
    $stock = $startStock;                       // laufender Bestand
    foreach ($budgets as $bid => $lbl) if (!isset($stock[$bid])) $stock[$bid] = 0.0;

    // Timeline startet mit Baseline am Fensteranfang
    $timelineMap = [];                          // label -> stock-Snapshot
    $timelineMap[$dateFrom] = $stock;

    $flow = [];                                 // budget -> [count, zugang, abgang]
    foreach ($budgets as $bid => $lbl) $flow[$bid] = ['count' => 0, 'zugang' => 0.0, 'abgang' => 0.0];

    $changes = [];                              // Detailliste

    foreach ($events as $e) {
        if ($e['ts'] < $fromStartUnix || $e['ts'] > $toEndUnix) continue;

        $ba = $e['budAlt'];
        $bn = $e['budNeu'];
        $oc = $e['oldC'];
        $nc = $e['newC'];

        // Bestand anpassen
        if (!isset($stock[$ba])) $stock[$ba] = 0.0;
        if (!isset($stock[$bn])) $stock[$bn] = 0.0;
        $stock[$ba] -= $oc;
        $stock[$bn] += $nc;

        $relevant = ($oc != $nc) || ($ba != $bn);
        if (!$relevant) continue;

        // Fluss
        if ($ba != $bn) {                       // Umbuchung
            $flow[$ba]['abgang'] += $oc;
            $flow[$ba]['count']++;
            $flow[$bn]['zugang'] += $nc;
            $flow[$bn]['count']++;
        } else {                                // gleiche Budget-Zelle: Mengen-/Preisänderung
            $d = $nc - $oc;
            if ($d >= 0) $flow[$bn]['zugang'] += $d; else $flow[$bn]['abgang'] += -$d;
            $flow[$bn]['count']++;
        }

        // Timeline-Snapshot (letzter Stand je Zeitlabel gewinnt)
        $timelineMap[$e['tsLabel']] = $stock;

        // Detailzeile
        $varLbl = ($e['varAlt'] === $e['varNeu'])
            ? ($varName[$e['varNeu']] ?? $e['varNeu'])
            : (($varName[$e['varAlt']] ?? $e['varAlt']) . ' -> ' . ($varName[$e['varNeu']] ?? $e['varNeu']));

        $changes[] = [
            $e['tsLabel'],
            $e['user'],
            $e['raum'],
            $e['elName'],
            $varLbl,
            $budgets[$ba] ?? ('#' . $ba),
            $budgets[$bn] ?? ('#' . $bn),
            ($e['anzAlt'] === null ? '' : (int)$e['anzAlt']),
            ($e['anzNeu'] === null ? '' : (int)$e['anzNeu']),
            round($nc - $oc, 2),
            $ba,     // rohe Budget-IDs für Client-Filter
            $bn
        ];
    }

    $endStock = $stock;

    // Timeline in geordnete Liste wandeln
    $timeline = [];
    foreach ($timelineMap as $label => $snap) {
        $clean = [];
        foreach ($snap as $bid => $val) $clean[$bid] = round($val, 2);
        $timeline[] = ['t' => $label, 'stock' => $clean];
    }

    // Netto je Budget aus Bestandsdifferenz
    $flowOut = [];
    foreach ($budgets as $bid => $lbl) {
        $netto = round(($endStock[$bid] ?? 0) - ($startStock[$bid] ?? 0), 2);
        $flowOut[$bid] = [
            'label' => $lbl,
            'count' => $flow[$bid]['count'],
            'zugang' => round($flow[$bid]['zugang'], 2),
            'abgang' => round($flow[$bid]['abgang'], 2),
            'netto' => $netto,
            'start' => round($startStock[$bid] ?? 0, 2),
            'end' => round($endStock[$bid] ?? 0, 2),
        ];
    }

    $budgetsOut = [];
    foreach ($budgets as $bid => $lbl) $budgetsOut[] = ['id' => $bid, 'label' => $lbl];

    $mysqli->close();
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'meta' => [
            'from' => $dateFrom,
            'to' => $dateTo,
            'onlyStandort1' => $onlyStandort1,
            'priceBasis' => 'aktuelle Kosten',
        ],
        'budgets' => $budgetsOut,
        'timeline' => $timeline,
        'flow' => $flowOut,
        'changes' => $changes,
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    if (isset($mysqli) && $mysqli) $mysqli->close();
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
