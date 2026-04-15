<?php
require_once 'utils/_utils.php';
check_login();
header('Content-Type: application/json');

$lot_id = (int)($_POST['lot_id'] ?? 0);
$notiz  = $_POST['notiz'] ?? '';

if (!$lot_id) {
    echo json_encode(['success' => false, 'error' => 'Ungültige Los-ID']);
    exit;
}

$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare("UPDATE tabelle_lose_extern SET Notiz = ? WHERE idtabelle_Lose_Extern = ?");
$stmt->bind_param("si", $notiz, $lot_id);
$stmt->execute();

echo json_encode(['success' => $stmt->affected_rows >= 0]);
$mysqli->close();
?>