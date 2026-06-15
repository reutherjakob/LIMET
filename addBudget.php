<?php
// addBudget.php — neues Projektbudget anlegen
require_once 'utils/_utils.php';
check_login();

header('Content-Type: application/json');

$projectID  = (int)($_SESSION['projectID'] ?? 0);
$budgetNr   = trim($_POST['Budgetnummer'] ?? '');
$budgetName = trim($_POST['Budgetname'] ?? '');

if ($projectID === 0) {
    echo json_encode(['status' => 'error', 'msg' => 'Kein Projekt ausgewählt.']);
    exit;
}
if ($budgetNr === '' || $budgetName === '') {
    echo json_encode(['status' => 'error', 'msg' => 'Budgetnummer und Budgetname sind Pflichtfelder.']);
    exit;
}

// varchar(45) Längenbegrenzung absichern
$budgetNr   = mb_substr($budgetNr, 0, 45);
$budgetName = mb_substr($budgetName, 0, 45);

$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare("
    INSERT INTO `LIMET_RB`.`tabelle_projektbudgets`
        (`tabelle_projekte_idTABELLE_Projekte`, `Budgetnummer`, `Budgetname`, `status`)
    VALUES (?, ?, ?, 0)
");
$stmt->bind_param('iss', $projectID, $budgetNr, $budgetName);

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'msg' => 'Budget angelegt.', 'id' => $stmt->insert_id]);
} else {
    echo json_encode(['status' => 'error', 'msg' => $stmt->error]);
}

$stmt->close();
$mysqli->close();