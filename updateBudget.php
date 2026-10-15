<?php

// 26 FX - unchecked#
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();

header('Content-Type: application/json; charset=utf-8');

$budgetID = isset($_POST['budgetID'])
    ? (int)$_POST['budgetID']
    : 0;

$budgetNr = isset($_POST['Budgetnummer'])
    ? trim($_POST['Budgetnummer'])
    : '';

$budgetName = isset($_POST['Budgetname'])
    ? trim($_POST['Budgetname'])
    : '';

if ($budgetID <= 0) {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Keine gültige Budget-ID übergeben.'
    ]);
    exit;
}

if ($budgetNr === '' || $budgetName === '') {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Budgetnummer und Budgetname sind Pflichtfelder.'
    ]);
    exit;
}

$budgetNr = mb_substr($budgetNr, 0, 45);
$budgetName = mb_substr($budgetName, 0, 45);

$mysqli = utils_connect_sql();

$stmt = $mysqli->prepare("
    UPDATE `LIMET_RB`.`tabelle_projektbudgets`
    SET
        `Budgetnummer` = ?,
        `Budgetname` = ?
    WHERE
        `idtabelle_projektbudgets` = ?
");

if (!$stmt) {
    echo json_encode([
        'status' => 'error',
        'msg' => 'SQL-Prepare-Fehler: ' . $mysqli->error
    ]);
    exit;
}

$stmt->bind_param(
    'ssi',
    $budgetNr,
    $budgetName,
    $budgetID
);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'status' => 'ok',
            'msg' => 'Budget aktualisiert.'
        ]);
    } else {
        echo json_encode([
            'status' => 'ok',
            'msg' => 'Keine Änderungen vorgenommen.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Fehler beim Aktualisieren: ' . $stmt->error
    ]);
}

$stmt->close();
$mysqli->close();