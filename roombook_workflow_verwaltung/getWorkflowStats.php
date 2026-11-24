<?php
// getWorkflowStats.php – Daten für die Workflow-Übersicht (JSON).
//   Liefert ALLE Workflows (projektübergreifend) inkl. Typ, Schritte,
//   zugeordneter Projekte und tatsächlicher Verwendung in Losen.
//   Flag inProject = Workflow ist dem aktiven Projekt zugeordnet.
require_once '../_utils/_utils.php';
require_once __DIR__ . '/workflow_helpers.php';
check_login();

header('Content-Type: application/json');

$projectID = (int)($_SESSION['projectID'] ?? 0);

$mysqli  = utils_connect_sql();
$nameCol = wf_name_column($mysqli);
$typPk   = wf_pk_column($mysqli, 'tabelle_workflowtyp');
$typName = wf_text_column($mysqli, 'tabelle_workflowtyp', $typPk);

$wfs = [];

// 1) Alle Workflows inkl. Typ
$res = $mysqli->query("
    SELECT w.idtabelle_workflow AS id,
           w.`$nameCol`         AS name,
           t.`$typName`         AS typ
    FROM tabelle_workflow w
    LEFT JOIN tabelle_workflowtyp t
      ON t.`$typPk` = w.tabelle_workflowtyp_idtabelle_workflowtyp
    ORDER BY name
");
while ($r = $res->fetch_assoc()) {
    $id = (int)$r['id'];
    $wfs[$id] = [
        'id'               => $id,
        'name'             => (string)($r['name'] ?? ('Workflow ' . $id)),
        'typ'              => (string)($r['typ'] ?? ''),
        'inProject'        => false,
        'steps'            => [],
        'assignedProjects' => [],
        'usedInProjects'   => [],
        'loseTotal'        => 0,
    ];
}
$res->free();

// 2) Alle Schritte (sortiert nach Reihenfolge)
$res = $mysqli->query("
    SELECT wh.tabelle_workflow_idtabelle_workflow AS wf,
           wt.idtabelle_wofklowteil AS workflowteilId,
           wt.aufgabe               AS aufgabe,
           wh.Reihenfolgennummer    AS reihenfolge,
           wh.TageMinDanach         AS tageMinDanach
    FROM tabelle_workflow_has_tabelle_wofklowteil wh
    JOIN tabelle_workflowteil wt
      ON wt.idtabelle_wofklowteil = wh.tabelle_wofklowteil_idtabelle_wofklowteil
    ORDER BY wh.tabelle_workflow_idtabelle_workflow, wh.Reihenfolgennummer, wt.aufgabe
");
while ($r = $res->fetch_assoc()) {
    $id = (int)$r['wf'];
    if (!isset($wfs[$id])) continue;
    $wfs[$id]['steps'][] = [
        'workflowteilId' => (int)$r['workflowteilId'],
        'aufgabe'        => (string)$r['aufgabe'],
        'reihenfolge'    => (int)$r['reihenfolge'],
        'tageMinDanach'  => (int)$r['tageMinDanach'],
    ];
}
$res->free();

// 3) Zugeordnete Projekte
$res = $mysqli->query("
    SELECT wp.tabelle_workflow_idtabelle_workflow AS wf,
           p.idTABELLE_Projekte AS id,
           p.Projektname        AS name
    FROM tabelle_workflow_has_tabelle_projekte wp
    JOIN tabelle_projekte p
      ON p.idTABELLE_Projekte = wp.tabelle_projekte_idTABELLE_Projekte
    ORDER BY p.Projektname
");
while ($r = $res->fetch_assoc()) {
    $id = (int)$r['wf'];
    if (!isset($wfs[$id])) continue;
    $pid = (int)$r['id'];
    $wfs[$id]['assignedProjects'][] = ['id' => $pid, 'name' => (string)$r['name']];
    if ($pid === $projectID) $wfs[$id]['inProject'] = true;
}
$res->free();

// 4) Tatsächliche Verwendung in Losen, je Projekt gezählt
$res = $mysqli->query("
    SELECT lw.tabelle_workflow_idtabelle_workflow AS wf,
           p.idTABELLE_Projekte AS id,
           p.Projektname        AS name,
           COUNT(*)             AS lose
    FROM tabelle_lot_workflow lw
    JOIN tabelle_lose_extern le
      ON le.idtabelle_Lose_Extern = lw.tabelle_lose_extern_idtabelle_Lose_Extern
    JOIN tabelle_projekte p
      ON p.idTABELLE_Projekte = le.tabelle_projekte_idTABELLE_Projekte
    GROUP BY lw.tabelle_workflow_idtabelle_workflow, p.idTABELLE_Projekte, p.Projektname
    ORDER BY p.Projektname
");
while ($r = $res->fetch_assoc()) {
    $id = (int)$r['wf'];
    if (!isset($wfs[$id])) continue;
    $wfs[$id]['usedInProjects'][] = [
        'id'   => (int)$r['id'],
        'name' => (string)$r['name'],
        'lose' => (int)$r['lose'],
    ];
    $wfs[$id]['loseTotal'] += (int)$r['lose'];
}
$res->free();
$mysqli->close();

// Projekt-Workflows zuerst, danach alphabetisch
$list = array_values($wfs);
usort($list, function ($a, $b) {
    if ($a['inProject'] !== $b['inProject']) return $a['inProject'] ? -1 : 1;
    return strcasecmp($a['name'], $b['name']);
});

echo json_encode([
    'currentProjectId' => $projectID,
    'workflows'        => $list,
]);
?>
