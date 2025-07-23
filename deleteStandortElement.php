<?php
require_once 'utils/_utils.php';
init_page_serversides();
check_login(); // session/login check

if (!isset($_GET['standortID']) || !is_numeric($_GET['standortID'])) {
    echo "Ungültige Standort-ID.";
    exit;
}

$standortID = (int)$_GET['standortID'];

// There is an optional $verwendungID parameter, but it is not used in the deletion:
$verwendungID = isset($_GET['verwendungID']) ? (int)$_GET['verwendungID'] : null;

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
