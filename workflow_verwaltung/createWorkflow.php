<?php
// createWorkflow.php – Legt einen neuen Workflow an, verknüpft ihn mit dem Projekt
//                      und fügt optional direkt Schritte hinzu.
require_once '../utils/_utils.php';
require_once __DIR__ . '/workflow_helpers.php';
check_login();

header('Content-Type: application/json');

$projectID     = (int)($_SESSION['projectID'] ?? 0);
$name          = trim((string)($_POST['name'] ?? ''));
$workflowtypID = filter_input(INPUT_POST, 'workflowtypID', FILTER_VALIDATE_INT) ?: 0;
$stepsRaw      = (string)($_POST['steps'] ?? '[]');

if (!$projectID) {
    echo json_encode(['status' => 'error', 'msg' => 'Kein Projekt aktiv.']);
    exit;
}
if ($name === '') {
    echo json_encode(['status' => 'error', 'msg' => 'Bitte eine Bezeichnung angeben.']);
    exit;
}
if ($workflowtypID <= 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Bitte einen Workflow-Typ wählen.']);
    exit;
}

// Schritte parsen + validieren
$steps = json_decode($stepsRaw, true);
if (!is_array($steps)) $steps = [];
$cleanSteps = [];
foreach ($steps as $s) {
    $tid = (int)($s['workflowteilId'] ?? 0);
    if ($tid <= 0) continue;
    $cleanSteps[] = [
        'teilId'      => $tid,
        'reihenfolge' => max(0, (int)($s['reihenfolge'] ?? 0)),
        'tage'        => max(0, (int)($s['tageMinDanach'] ?? 0)),
    ];
}

$mysqli  = utils_connect_sql();
$pkCol   = wf_pk_column($mysqli, 'tabelle_workflow');   // i. d. R. idtabelle_workflow
$nameCol = wf_name_column($mysqli);
$hasName = ($nameCol !== $pkCol); // false -> tabelle_workflow hat keine eigene Namensspalte

$mysqli->begin_transaction();
try {
    // 1) Workflow-Kopf anlegen (inkl. Pflicht-FK auf den Workflow-Typ)
    if ($hasName) {
        $ins = $mysqli->prepare("
            INSERT INTO tabelle_workflow
                (`$nameCol`, `tabelle_workflowtyp_idtabelle_workflowtyp`)
            VALUES (?, ?)
        ");
        $ins->bind_param('si', $name, $workflowtypID);
    } else {
        // Kein eigenes Namensfeld -> nur Typ setzen
        $ins = $mysqli->prepare("
            INSERT INTO tabelle_workflow
                (`tabelle_workflowtyp_idtabelle_workflowtyp`)
            VALUES (?)
        ");
        $ins->bind_param('i', $workflowtypID);
    }
    if (!$ins->execute()) throw new RuntimeException($ins->error);
    $newId = $ins->insert_id;
    $ins->close();

    // 2) Mit Projekt verknüpfen
    $lnk = $mysqli->prepare("
        INSERT INTO tabelle_workflow_has_tabelle_projekte
            (tabelle_workflow_idtabelle_workflow, tabelle_projekte_idTABELLE_Projekte)
        VALUES (?, ?)
    ");
    $lnk->bind_param('ii', $newId, $projectID);
    if (!$lnk->execute()) throw new RuntimeException($lnk->error);
    $lnk->close();

    // 3) Schritte (optional)
    if ($cleanSteps) {
        $st = $mysqli->prepare("
            INSERT INTO tabelle_workflow_has_tabelle_wofklowteil
                (tabelle_workflow_idtabelle_workflow,
                 tabelle_wofklowteil_idtabelle_wofklowteil,
                 Reihenfolgennummer, TageMinDanach)
            VALUES (?, ?, ?, ?)
        ");
        foreach ($cleanSteps as $s) {
            $st->bind_param('iiii', $newId, $s['teilId'], $s['reihenfolge'], $s['tage']);
            if (!$st->execute()) throw new RuntimeException($st->error);
        }
        $st->close();
    }

    $mysqli->commit();
    echo json_encode(['status' => 'ok', 'id' => $newId]);
} catch (Throwable $e) {
    $mysqli->rollback();
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
$mysqli->close();
?>