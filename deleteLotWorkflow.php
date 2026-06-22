<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();
$lotId  = getPostInt('lotID');

if ($lotId <= 0) {
    echo "Kein gültiges Los übergeben!";
    $mysqli->close();
    exit;
}

$stmt = $mysqli->prepare(
    "DELETE FROM tabelle_lot_workflow
     WHERE tabelle_lose_extern_idtabelle_Lose_Extern = ?"
);
$stmt->bind_param("i", $lotId);

if ($stmt->execute()) {
    $deleted = $stmt->affected_rows;
    echo "Workflow entfernt (" . $deleted . " Schritt(e) gelöscht).";
} else {
    echo "Fehler beim Entfernen des Workflows: " . $mysqli->error;
}

$stmt->close();
$mysqli->close();
?>