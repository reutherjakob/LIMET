<?php
// updateTenderWorkflowDates_holidays.php
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$lotID = getPostInt('lotID');

// ─── Austrian public holidays (dynamic) ───────────────────────────────────────
function getAustrianHolidays(int $year): array {
    // Easter Sunday (Gauss algorithm)
    $a = $year % 19;
    $b = intdiv($year, 100);
    $c = $year % 100;
    $d = intdiv($b, 4);
    $e = $b % 4;
    $f = intdiv($b + 8, 25);
    $g = intdiv($b - $f + 1, 3);
    $h = (19 * $a + $b - $d - $g + 15) % 30;
    $i = intdiv($c, 4);
    $k = $c % 4;
    $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
    $m = intdiv($a + 11 * $h + 22 * $l, 451);
    $month = intdiv($h + $l - 7 * $m + 114, 31);
    $day   = (($h + $l - 7 * $m + 114) % 31) + 1;
    $easter = mktime(0, 0, 0, $month, $day, $year);

    $holidays = [
        "$year-01-01", // Neujahr
        "$year-01-06", // Heilige Drei Könige
        "$year-05-01", // Staatsfeiertag
        "$year-08-15", // Mariä Himmelfahrt
        "$year-10-26", // Nationalfeiertag
        "$year-11-01", // Allerheiligen
        "$year-12-08", // Mariä Empfängnis
        "$year-12-25", // Christtag
        "$year-12-26", // Stefanitag

        date('Y-m-d', strtotime('+1 day',   $easter)), // Ostermontag
        date('Y-m-d', strtotime('+39 days', $easter)), // Christi Himmelfahrt
        date('Y-m-d', strtotime('+50 days', $easter)), // Pfingstmontag
        date('Y-m-d', strtotime('+60 days', $easter)), // Fronleichnam
    ];

    return array_flip($holidays); // use as lookup: isset($holidays[$dateStr])
}

/**
 * Subtract $workdays business days from $fromDate, skipping weekends and
 * Austrian public holidays. Returns the resulting date as 'Y-m-d'.
 */
function subtractWorkdays(string $fromDate, int $workdays): string {
    $ts          = strtotime($fromDate);
    $holidayCache = []; // keyed by year

    while ($workdays > 0) {
        $ts -= 86400; // one calendar day back
        $year = (int)date('Y', $ts);

        if (!isset($holidayCache[$year])) {
            $holidayCache[$year] = getAustrianHolidays($year);
        }

        $dow     = (int)date('N', $ts); // 1=Mon … 7=Sun
        $dateStr = date('Y-m-d', $ts);

        // Skip weekends and holidays
        if ($dow >= 6 || isset($holidayCache[$year][$dateStr])) {
            continue;
        }

        $workdays--;
    }

    return date('Y-m-d', $ts);
}
// ─────────────────────────────────────────────────────────────────────────────

// Fetch all workflow steps DESC (last step = highest Reihenfolgennummer first)
$sql = "SELECT tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer, 
               DATE_FORMAT(DATE(tabelle_lot_workflow.Timestamp_Soll), '%Y-%m-%d') AS Timestamp_Soll, 
               tabelle_workflow_has_tabelle_wofklowteil.TageMinDanach,
               tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern, 
               tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow,
               tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil 
        FROM tabelle_lot_workflow 
        INNER JOIN tabelle_workflow_has_tabelle_wofklowteil
            ON tabelle_lot_workflow.tabelle_workflow_idtabelle_workflow =
               tabelle_workflow_has_tabelle_wofklowteil.tabelle_workflow_idtabelle_workflow 
               AND tabelle_lot_workflow.tabelle_wofklowteil_idtabelle_wofklowteil =
                   tabelle_workflow_has_tabelle_wofklowteil.tabelle_wofklowteil_idtabelle_wofklowteil 
        WHERE tabelle_lot_workflow.tabelle_lose_extern_idtabelle_Lose_Extern = ? 
        ORDER BY tabelle_workflow_has_tabelle_wofklowteil.Reihenfolgennummer DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $lotID);
$stmt->execute();
$result = $stmt->get_result();

$workflowTeile = [];
while ($row = $result->fetch_assoc()) {
    $workflowTeile[] = $row;
}
$stmt->close();

if (empty($workflowTeile)) {
    echo "Fehler: Keine Workflow-Schritte gefunden.";
    $mysqli->close();
    exit;
}

// Index 0 = last step (highest Reihenfolgennummer) = the anchor
$anchorDate           = $workflowTeile[0]['Timestamp_Soll'];
$anchorWorkflowTeilId = (int)$workflowTeile[0]['tabelle_wofklowteil_idtabelle_wofklowteil'];

if (empty($anchorDate) || $anchorDate === '0000-00-00' || $anchorDate === null) {
    echo "Fehler: Der letzte Workflow-Schritt hat kein Soll-Datum. Bitte zuerst dieses Datum setzen.";
    $mysqli->close();
    exit;
}

$updateSql = "UPDATE `tabelle_lot_workflow` 
              SET `Timestamp_Soll` = ? 
              WHERE `tabelle_lose_extern_idtabelle_Lose_Extern` = ? 
                AND `tabelle_workflow_idtabelle_workflow` = ? 
                AND `tabelle_wofklowteil_idtabelle_wofklowteil` = ?";
$updateStmt = $mysqli->prepare($updateSql);

$errors      = 0;
$updated     = 0;
$currentDate = $anchorDate; // walk backwards from anchor

foreach ($workflowTeile as $index => $step) {
    // NEVER touch the anchor row — its date is the source of truth
    if ($index === 0) {
        continue;
    }

    // Days to subtract = TageMinDanach of THIS step
    // (= minimum workdays between this step and the next one)
    $daysBefore = (int)$workflowTeile[$index]['TageMinDanach'];

    // Subtract working days only (skips weekends + Austrian holidays)
    $newDate = subtractWorkdays($currentDate, $daysBefore);

    $workflowId     = (int)$step['tabelle_workflow_idtabelle_workflow'];
    $workflowTeilId = (int)$step['tabelle_wofklowteil_idtabelle_wofklowteil'];

    // Safety guard: never accidentally overwrite the anchor
    if ($workflowTeilId === $anchorWorkflowTeilId) {
        continue;
    }

    $updateStmt->bind_param('siii', $newDate, $lotID, $workflowId, $workflowTeilId);
    if ($updateStmt->execute()) {
        $updated++;
    } else {
        $errors++;
    }

    $currentDate = $newDate; // this becomes the reference for the next earlier step
}

$updateStmt->close();
$mysqli->close();

if ($errors === 0) {
    echo "Workflow-Daten erfolgreich berechnet: {$updated} Schritte aktualisiert.";
} else {
    echo "Fehler: {$errors} Schritte konnten nicht aktualisiert werden, {$updated} erfolgreich.";
}
?>