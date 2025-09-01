<?php
session_start();
include "../../utils/_utils.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

$projectId = (int)($_SESSION["projectID"] ?? 0);
$raumbereiche = $_POST['raumbereiche'] ?? [];

if ($projectId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ungültige oder fehlende Projekt-ID']);
    exit;
}

if (!is_array($raumbereiche) || count($raumbereiche) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Keine Raumbereiche übermittelt']);
    exit;
}

$conn = utils_connect_sql();

try {
    $placeholders = implode(',', array_fill(0, count($raumbereiche), '?'));
    $sql = "SELECT idTABELLE_Räume AS id, Raumnr, Raumbezeichnung, `Raumbereich Nutzer` 
            FROM tabelle_räume
            WHERE tabelle_projekte_idTABELLE_Projekte = ? 
              AND `Raumbereich Nutzer` IN ($placeholders)
              AND Entfallen = 0
            ORDER BY Raumnr";

    $stmt = $conn->prepare($sql);
    if (!$stmt) throw new Exception("Prepare statement fehlgeschlagen: " . $conn->error);

    $types = 'i' . str_repeat('s', count($raumbereiche));
    $params = array_merge([$projectId], $raumbereiche);

    // Referenzen für bind_param vorbereiten
    $refs = [];
    foreach ($params as $key => $value) {
        $refs[$key] = &$params[$key];
    }

    call_user_func_array([$stmt, 'bind_param'], array_merge([$types], $refs));

    $stmt->execute();
    $result = $stmt->get_result();

    $raeume = [];
    while ($row = $result->fetch_assoc()) {
        $raeume[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode([
        'success' => true,
        'data' => $raeume
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    $conn->close();
}
