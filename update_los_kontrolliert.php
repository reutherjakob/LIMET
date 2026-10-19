<?php

// 26 FX - unchecked#
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();

header('Content-Type: application/json');

if (empty($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Kein Benutzer eingeloggt']);
    exit;
}

$lot_id    = (int)$_POST['lot_id'];
$reset     = (int)($_POST['reset'] ?? 0);
$username  = $_SESSION['username'];

$mysqli = utils_connect_sql();

if ($reset) {
    $stmt = $mysqli->prepare("
        UPDATE tabelle_lose_extern 
        SET kontrolle_preise_in_db_user = NULL  
        WHERE idtabelle_Lose_Extern = ?
    ");
    $stmt->bind_param("i", $lot_id);
} else {
    $stmt = $mysqli->prepare("
        UPDATE tabelle_lose_extern 
        SET kontrolle_preise_in_db_user = ? 
        WHERE idtabelle_Lose_Extern = ?
    ");
    $stmt->bind_param("si", $username, $lot_id);
}

$stmt->execute();
echo json_encode(['success' => $stmt->affected_rows >= 0]);
$mysqli->close();
?>