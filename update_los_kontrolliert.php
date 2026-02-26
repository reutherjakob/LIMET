<?php
session_start();
require_once 'utils/_utils.php';
header('Content-Type: application/json');


if (empty($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Kein Benutzer eingeloggt']);
    exit;
}

$projekt_id = (int)$_POST['projekt_id'];
$lot_id = (int)$_POST['lot_id'];
$username = $_SESSION['username'];


$mysqli = utils_connect_sql();
$sql = "UPDATE tabelle_lose_extern 
        SET kontrolle_preise_in_db_user = ? 
        WHERE idtabelle_Lose_Extern = ? AND tabelle_projekte_idTABELLE_Projekte = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sii", $username, $lot_id, $projekt_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $mysqli->error]);
}

$mysqli->close();
?>
