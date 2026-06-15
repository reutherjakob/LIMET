<?php
// updateWorkflowStep.php – Aktualisiert Reihenfolge und/oder Mindesttage eines Workflow-Schritts.
require_once '../utils/_utils.php';
require_once __DIR__ . '/workflow_helpers.php';
check_login();

header('Content-Type: application/json');

$projectID      = (int)($_SESSION['projectID'] ?? 0);
$workflowID     = filter_input(INPUT_POST, 'workflowID',     FILTER_VALIDATE_INT);
$workflowteilID = filter_input(INPUT_POST, 'workflowteilID', FILTER_VALIDATE_INT);
$reihenfolge    = filter_input(INPUT_POST, 'reihenfolge',    FILTER_VALIDATE_INT);
$tageMinDanach  = filter_input(INPUT_POST, 'tageMinDanach',  FILTER_VALIDATE_INT);

if (!$workflowID || !$workflowteilID || $reihenfolge === false || $tageMinDanach === false
    || $reihenfolge === null || $tageMinDanach === null || !$projectID) {
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter.']);
    exit;
}
$reihenfolge   = max(0, $reihenfolge);
$tageMinDanach = max(0, $tageMinDanach);

$mysqli = utils_connect_sql();

if (!wf_belongs_to_project($mysqli, $workflowID, $projectID)) {
    $mysqli->close();
    echo json_encode(['status' => 'error', 'msg' => 'Workflow gehört nicht zum Projekt.']);
    exit;
}

// Schutz: Die Schritte hängen am Workflow, nicht am Projekt. Ist der Workflow
// noch weiteren Projekten zugeordnet, würde diese Änderung jene Projekte
// ebenfalls verändern -> blockieren.
if (wf_shared_with_other_project($mysqli, $workflowID, $projectID)) {
    $mysqli->close();
    echo json_encode([
        'status' => 'error',
        'msg'    => 'Dieser Workflow wird von weiteren Projekten genutzt – eine Änderung würde diese ebenfalls betreffen.'
    ]);
    exit;
}

$stmt = $mysqli->prepare("
    UPDATE tabelle_workflow_has_tabelle_wofklowteil
    SET Reihenfolgennummer = ?, TageMinDanach = ?
    WHERE tabelle_workflow_idtabelle_workflow = ?
      AND tabelle_wofklowteil_idtabelle_wofklowteil = ?
");
$stmt->bind_param('iiii', $reihenfolge, $tageMinDanach, $workflowID, $workflowteilID);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok']);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>