<?php
require_once 'utils/_utils.php';
check_login();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$lot_id     = (int)($_POST['lot_id']     ?? 0);
$preis_status = (int)($_POST['preis_status'] ?? 0);
$username   = $_SESSION['username'] ?? '';

if (!$lot_id) {
    echo json_encode(['error' => 'Missing lot_id']);
    exit;
}

$mysqli = utils_connect_sql();

switch ($preis_status) {
    case 0:
        $stmt = $mysqli->prepare("
            UPDATE tabelle_lose_extern 
            SET preise_in_db = 0,
                preise_in_db_user = NULL,
                kontrolle_preise_in_db_user = NULL
            WHERE idtabelle_Lose_Extern = ?
        ");
        $stmt->bind_param("i", $lot_id);
        break;

    case 1:
        $stmt = $mysqli->prepare("
            UPDATE tabelle_lose_extern 
            SET preise_in_db = 1,
                preise_in_db_user = NULL,
                kontrolle_preise_in_db_user = NULL
            WHERE idtabelle_Lose_Extern = ?
        ");
        $stmt->bind_param("i", $lot_id);
        break;

    case 2:
        if (empty($username)) {
            echo json_encode(['error' => 'Kein Benutzer in Session']);
            exit;
        }
        $stmt = $mysqli->prepare("
            UPDATE tabelle_lose_extern 
            SET preise_in_db = 2,
                preise_in_db_user = ?,
                kontrolle_preise_in_db_user = NULL
            WHERE idtabelle_Lose_Extern = ?
        ");
        $stmt->bind_param("si", $username, $lot_id);
        break;

    default:
        echo json_encode(['error' => 'Ungültiger Status']);
        exit;
}

$stmt->execute();
echo json_encode(['success' => $stmt->affected_rows >= 0]);
$stmt->close();
$mysqli->close();
?>