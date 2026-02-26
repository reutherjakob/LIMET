<?php
require_once 'utils/_utils.php';
check_login();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$lot_id = (int)$_POST['lot_id'] ?? 0;
$projekt_id = (int)$_POST['projekt_id'] ?? 0;
$preis_status = (int)$_POST['preis_status'] ?? 0;

if (!$lot_id || !$projekt_id) {
    echo json_encode(['error' => 'Missing lot_id or projekt_id']);
    exit;
}

$mysqli = utils_connect_sql();

$stmt = $mysqli->prepare("
    UPDATE tabelle_lose_extern 
    SET preise_in_db = ?
    WHERE idtabelle_Lose_Extern = ? AND tabelle_projekte_idTABELLE_Projekte = ?
");

$stmt->bind_param("iii", $preis_status, $lot_id, $projekt_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'preis_status' => $preis_status,

        ]);
    } else {
        echo json_encode(['error' => 'No rows updated']);
    }
} else {
    echo json_encode(['error' => 'Database error: ' . $mysqli->error]);
}

$stmt->close();
$mysqli->close();
?>
