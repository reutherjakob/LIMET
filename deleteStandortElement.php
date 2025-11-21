<?php
// 25FX
require_once 'utils/_utils.php';
check_login(); // session/login check

$standortID = getPostInt('standortID');
if ($standortID === 0) { // assuming 0 indicates invalid or missing
    echo "Ungültige Standort-ID.";
    exit;
}

$mysqli = utils_connect_sql();

$sql = "DELETE FROM tabelle_verwendungselemente WHERE id_Standortelement = ?";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo "Fehler bei der Vorbereitung der Anfrage: " . $mysqli->error;
    exit;
}

$stmt->bind_param("i", $standortID);

if ($stmt->execute()) {
    echo "Standortelement wurde erfolgreich gelöscht.";
} else {
    echo "Fehler beim Löschen des Standortelements: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
s