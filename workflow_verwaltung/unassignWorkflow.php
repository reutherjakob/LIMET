<?php
// unassignWorkflow.php – Löst die Verknüpfung eines Workflows mit dem aktuellen Projekt.
// Hinweis: Der globale Workflow selbst wird NICHT gelöscht (kann von anderen Projekten /
//          Losen via tabelle_lot_workflow weiterverwendet werden).
require_once '../utils/_utils.php';
require_once __DIR__ . '/workflow_helpers.php';
check_login();

header('Content-Type: application/json');

$projectID  = (int)($_SESSION['projectID'] ?? 0);
$workflowID = filter_input(INPUT_POST, 'workflowID', FILTER_VALIDATE_INT);

if (!$workflowID || !$projectID) {
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter.']);
    exit;
}

$mysqli = utils_connect_sql();


if (wf_in_use_in_current_project($mysqli, (int)$workflowID)) {
    $mysqli->close();
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Workflow ist in Benutzung und kann nicht vom Projekt gelöst werden.'
    ]);
    exit;
}

$stmt = $mysqli->prepare("
    DELETE FROM tabelle_workflow_has_tabelle_projekte
    WHERE tabelle_workflow_idtabelle_workflow = ?
      AND tabelle_projekte_idTABELLE_Projekte = ?
");
$stmt->bind_param('ii', $workflowID, $projectID);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>