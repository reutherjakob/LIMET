<?php
// removeWorkflowStep.php – Entfernt einen Schritt aus einem Workflow.
require_once '../utils/_utils.php';
require_once __DIR__ . '/workflow_helpers.php';
check_login();

header('Content-Type: application/json');

$projectID      = (int)($_SESSION['projectID'] ?? 0);
$workflowID     = filter_input(INPUT_POST, 'workflowID',     FILTER_VALIDATE_INT);
$workflowteilID = filter_input(INPUT_POST, 'workflowteilID', FILTER_VALIDATE_INT);

if (!$workflowID || !$workflowteilID || !$projectID) {
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter.']);
    exit;
}

$mysqli = utils_connect_sql();

if (!wf_belongs_to_project($mysqli, $workflowID, $projectID)) {
    $mysqli->close();
    echo json_encode(['status' => 'error', 'msg' => 'Workflow gehört nicht zum Projekt.']);
    exit;
}


if (wf_in_use($mysqli, (int)$workflowID)) {
    $mysqli->close();
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Workflow ist in Benutzung – Schritte können nicht entfernt werden.'
    ]);
    exit;
}

$stmt = $mysqli->prepare("
    DELETE FROM tabelle_workflow_has_tabelle_wofklowteil
    WHERE tabelle_workflow_idtabelle_workflow = ?
      AND tabelle_wofklowteil_idtabelle_wofklowteil = ?
");
$stmt->bind_param('ii', $workflowID, $workflowteilID);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>