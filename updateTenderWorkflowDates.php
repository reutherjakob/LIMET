<?php
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$lotID = getPostInt('lotID');

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
$anchorDate = $workflowTeile[0]['Timestamp_Soll'];
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

$errors  = 0;
$updated = 0;
$currentDate = $anchorDate; // walk backwards from anchor

foreach ($workflowTeile as $index => $step) {
    // NEVER touch the anchor row — its date is the source of truth
    if ($index === 0) {
        continue;
    }

    // Days to subtract = TageMinDanach of the step that comes AFTER this one (index - 1)
    $daysBefore = (int)$workflowTeile[$index]['TageMinDanach'];

    $newDate = date('Y-m-d', strtotime($currentDate . " -{$daysBefore} days"));

    // Shift weekends to Friday
    $weekday = (int)date('N', strtotime($newDate));
    if ($weekday === 6) {
        $newDate = date('Y-m-d', strtotime($newDate . ' -1 day'));
    } elseif ($weekday === 7) {
        $newDate = date('Y-m-d', strtotime($newDate . ' -2 days'));
    }

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