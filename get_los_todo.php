<?php

require_once "utils/_utils.php";
init_page_serversides();

$mysqli = utils_connect_sql();
$id = getPostInt("lotID");

if ($id > 0) {
    $stmt = $mysqli->prepare("SELECT ToDo FROM tabelle_lose_ToDos WHERE id_tabelle_lose_extern = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="card mb-3">';
            echo '<div class="card-body">';
            echo htmlspecialchars($row['ToDo'], ENT_QUOTES, 'UTF-8'); // nl2br
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "Todo nicht gefunden.";
    }

} else {
    echo "Ungültige ID.";
}

$mysqli->close();