<?php

require_once "utils/_utils.php";
init_page_serversides();

$mysqli = utils_connect_sql();
$id = (int)($_GET['ID'] ?? 0);

if ($id > 0) {
    $stmt = $mysqli->prepare("SELECT ToDo FROM tabelle_lose_ToDos WHERE id_tabelle_lose_ToDos = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo nl2br(htmlspecialchars($row['ToDo'], ENT_QUOTES, 'UTF-8'));
    } else {
        echo "Todo nicht gefunden.";
    }
} else {
    echo "Ungültige ID.";
}

$mysqli->close();