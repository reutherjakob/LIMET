<?php
// renameWorkflow.php – Ändert die Bezeichnung eines Workflows.
//                      Nur für Workflows erlaubt, die dem aktiven Projekt zugeordnet sind.
require_once '../utils/_utils.php';
require_once __DIR__ . '/workflow_helpers.php';
check_login();

header('Content-Type: application/json');

$projectID  = (int)($_SESSION['projectID'] ?? 0);
$workflowID = filter_input(INPUT_POST, 'workflowID', FILTER_VALIDATE_INT);
$name       = trim((string)($_POST['name'] ?? ''));

if (!$workflowID || !$projectID) {
    echo json_encode(['status' => 'error', 'msg' => 'Ungültige Parameter.']);
    exit;
}
if ($name === '') {
    echo json_encode(['status' => 'error', 'msg' => 'Bitte eine Bezeichnung angeben.']);
    exit;
}

$mysqli = utils_connect_sql();

// Schutz: nur Workflows des aktiven Projekts umbenennen.
if (!wf_belongs_to_project($mysqli, (int)$workflowID, $projectID)) {
    echo json_encode(['status' => 'error', 'msg' => 'Workflow gehört nicht zum Projekt.']);
    $mysqli->close();
    exit;
}

$pkCol   = wf_pk_column($mysqli, 'tabelle_workflow');   // i. d. R. idtabelle_workflow
$nameCol = wf_name_column($mysqli);

// Keine eigene Namensspalte vorhanden -> Umbenennen nicht möglich.
if ($nameCol === $pkCol) {
    echo json_encode(['status' => 'error', 'msg' => 'Dieser Workflow besitzt keine Namensspalte.']);
    $mysqli->close();
    exit;
}

$stmt = $mysqli->prepare("
    UPDATE tabelle_workflow
       SET `$nameCol` = ?
     WHERE `$pkCol` = ?
");
$stmt->bind_param('si', $name, $workflowID);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'name' => $name]);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}
$stmt->close();
$mysqli->close();
?>