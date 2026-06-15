<?php
// getProjectWorkflows.php – Liefert alle Workflows des aktuellen Projektes inkl. Schritte (JSON)
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

// Workflows, die dem Projekt zugeordnet sind
$stmt = $mysqli->prepare("
    SELECT w.idtabelle_workflow AS id,
           w.`$nameCol`        AS name
    FROM tabelle_workflow w
    JOIN tabelle_workflow_has_tabelle_projekte wp
      ON wp.tabelle_workflow_idtabelle_workflow = w.idtabelle_workflow
    WHERE wp.tabelle_projekte_idTABELLE_Projekte = ?
    ORDER BY name
");
$stmt->bind_param('i', $projectID);
$stmt->execute();
$workflows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Schritte je Workflow (sortiert nach Reihenfolge)
$stepStmt = $mysqli->prepare("
    SELECT wt.idtabelle_wofklowteil AS workflowteilId,
           wt.aufgabe               AS aufgabe,
           wh.Reihenfolgennummer    AS reihenfolge,
           wh.TageMinDanach         AS tageMinDanach
    FROM tabelle_workflow_has_tabelle_wofklowteil wh
    JOIN tabelle_workflowteil wt
      ON wt.idtabelle_wofklowteil = wh.tabelle_wofklowteil_idtabelle_wofklowteil
    WHERE wh.tabelle_workflow_idtabelle_workflow = ?
    ORDER BY wh.Reihenfolgennummer, wt.aufgabe
");

foreach ($workflows as &$wf) {
    $wfId = (int)$wf['id'];
    $stepStmt->bind_param('i', $wfId);
    $stepStmt->execute();
    $steps = $stepStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // numerische Felder sauber typisieren
    foreach ($steps as &$s) {
        $s['workflowteilId'] = (int)$s['workflowteilId'];
        $s['reihenfolge']    = (int)$s['reihenfolge'];
        $s['tageMinDanach']  = (int)$s['tageMinDanach'];
    }
    unset($s);
    $wf['id']    = (int)$wf['id'];
    $wf['steps'] = $steps;
}
unset($wf);
$stepStmt->close();
$mysqli->close();

echo json_encode($workflows);
?>