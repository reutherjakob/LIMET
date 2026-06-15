<?php
// assignWorkflow.php – Verknüpft einen bereits bestehenden Workflow mit dem
//                      aktuellen Projekt (Gegenstück zu unassignWorkflow.php).
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

// Schon zugeordnet? -> nichts zu tun (kein Fehler).
if (wf_belongs_to_project($mysqli, (int)$workflowID, $projectID)) {
    echo json_encode(['status' => 'ok', 'msg' => 'Bereits zugeordnet.']);
    $mysqli->close();
    exit;
}

$stmt = $mysqli->prepare("
    INSERT INTO tabelle_workflow_has_tabelle_projekte
        (tabelle_workflow_idtabelle_workflow, tabelle_projekte_idTABELLE_Projekte)
    VALUES (?, ?)
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