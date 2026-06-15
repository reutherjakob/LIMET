<?php
// getUnassignedWorkflows.php – Liefert alle Workflows, die dem aktuellen Projekt
//                               NOCH NICHT zugeordnet sind (JSON).
//                               Grundlage für „Bestehenden Workflow hinzufügen“.
require_once '../utils/_utils.php';
require_once __DIR__ . '/workflow_helpers.php';
check_login();

header('Content-Type: application/json');

$projectID = (int)($_SESSION['projectID'] ?? 0);
if (!$projectID) {
    echo json_encode([]);
    exit;
}

$mysqli  = utils_connect_sql();
$nameCol = wf_name_column($mysqli);

$stmt = $mysqli->prepare("
    SELECT w.idtabelle_workflow AS id,
           w.`$nameCol`        AS name
    FROM tabelle_workflow w
    WHERE w.idtabelle_workflow NOT IN (
        SELECT tabelle_workflow_idtabelle_workflow
        FROM tabelle_workflow_has_tabelle_projekte
        WHERE tabelle_projekte_idTABELLE_Projekte = ?
    )
    ORDER BY name
");
$stmt->bind_param('i', $projectID);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();

foreach ($rows as &$r) { $r['id'] = (int)$r['id']; }
unset($r);

echo json_encode($rows);
?>