<?php
// addWorkflowStep.php – Fügt einem bestehenden Workflow einen Schritt hinzu.
require_once '../utils/_utils.php';
require_once __DIR__ . '/workflow_helpers.php';
check_login();

header('Content-Type: application/json');

$projectID      = (int)($_SESSION['projectID'] ?? 0);
$workflowID     = filter_input(INPUT_POST, 'workflowID',     FILTER_VALIDATE_INT);
$workflowteilID = filter_input(INPUT_POST, 'workflowteilID', FILTER_VALIDATE_INT);
$reihenfolge    = filter_input(INPUT_POST, 'reihenfolge',    FILTER_VALIDATE_INT);
$tageMinDanach  = filter_input(INPUT_POST, 'tageMinDanach',  FILTER_VALIDATE_INT);

if (!$workflowID || !$workflowteilID || !$projectID) {
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter.']);
    exit;
}
$reihenfolge   = max(0, (int)$reihenfolge);
$tageMinDanach = max(0, (int)$tageMinDanach);

$mysqli = utils_connect_sql();

if (!wf_belongs_to_project($mysqli, $workflowID, $projectID)) {
    $mysqli->close();
    echo json_encode(['status' => 'error', 'msg' => 'Workflow gehört nicht zum Projekt.']);
    exit;
}

$stmt = $mysqli->prepare("
    INSERT INTO tabelle_workflow_has_tabelle_wofklowteil
        (tabelle_workflow_idtabelle_workflow,
         tabelle_wofklowteil_idtabelle_wofklowteil,
         Reihenfolgennummer, TageMinDanach)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param('iiii', $workflowID, $workflowteilID, $reihenfolge, $tageMinDanach);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok']);
} elseif ($mysqli->errno === 1062) {
    echo json_encode(['status' => 'error', 'msg' => 'Diese Aufgabe ist im Workflow bereits enthalten.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>