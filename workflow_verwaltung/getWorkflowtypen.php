<?php
// getWorkflowtypen.php – Liefert die Workflow-Typen (tabelle_workflowtyp) als JSON.
require_once '../utils/_utils.php';
require_once __DIR__ . '/workflow_helpers.php';
check_login();

header('Content-Type: application/json');

$mysqli  = utils_connect_sql();
$pkCol   = wf_pk_column($mysqli, 'tabelle_workflowtyp');
$nameCol = wf_text_column($mysqli, 'tabelle_workflowtyp', $pkCol);

$stmt = $mysqli->prepare("
    SELECT `$pkCol` AS id, `$nameCol` AS name
    FROM tabelle_workflowtyp
    ORDER BY name
");
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();

foreach ($rows as &$r) { $r['id'] = (int)$r['id']; }
unset($r);

echo json_encode($rows);
?>