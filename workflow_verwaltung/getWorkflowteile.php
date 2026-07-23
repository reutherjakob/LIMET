<?php
// getWorkflowteile.php – Liefert verfügbare Aufgaben (tabelle_workflowteil) als JSON.
// Optional: workflowID übergeben -> bereits im Workflow verwendete Aufgaben werden ausgeblendet.
require_once '../utils/_utils.php';
require_once __DIR__ . '/workflow_helpers.php';
check_login();

header('Content-Type: application/json');

$projectID  = (int)($_SESSION['projectID'] ?? 0);
$workflowID = filter_input(INPUT_POST, 'workflowID', FILTER_VALIDATE_INT) ?: 0;

$mysqli = utils_connect_sql();

if ($workflowID) {
    // Nur erlauben, wenn der Workflow zum Projekt gehört
    if (!$projectID || !wf_belongs_to_project($mysqli, $workflowID, $projectID)) {
        $mysqli->close();
        echo json_encode([]);
        exit;
    }
    $stmt = $mysqli->prepare("
        SELECT idtabelle_wofklowteil AS id, aufgabe
        FROM tabelle_workflowteil
        WHERE idtabelle_wofklowteil NOT IN (
            SELECT tabelle_wofklowteil_idtabelle_wofklowteil
            FROM tabelle_workflow_has_tabelle_wofklowteil
            WHERE tabelle_workflow_idtabelle_workflow = ?
        )
        ORDER BY aufgabe
    ");
    $stmt->bind_param('i', $workflowID);
} else {
    $stmt = $mysqli->prepare("
        SELECT idtabelle_wofklowteil AS id, aufgabe
        FROM tabelle_workflowteil
        ORDER BY aufgabe
    ");
}

$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();

foreach ($rows as &$r) { $r['id'] = (int)$r['id']; }
unset($r);

echo json_encode($rows);
?>