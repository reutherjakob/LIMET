<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$columnFromPost = getPostString('column');
$value = getPostString('value');
$roomID = getPostInt('roomID');
$columnsDataJson = getPostString('columnsData');
$columnsData = json_decode($columnsDataJson, true) ?? [];

if ($roomID <= 0) {
    http_response_code(400);
    echo 'Ungültige Raum-ID';
    exit;
}
if (!is_array($columnsData) || !in_array($columnFromPost, $columnsData, true)) {
    http_response_code(400);
    echo 'Ungültige Spalte';
    exit;
}
$sql = "UPDATE tabelle_räume SET tabelle_räume.`$columnFromPost` = ? WHERE tabelle_räume.idTABELLE_Räume = ?";
$stmt = $mysqli->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    echo 'Fehler beim Vorbereiten des Statements';
    $mysqli->close();
    exit;
}
$stmt->bind_param('si', $value, $roomID);
if ($stmt->execute()) {
    echo 'Erfolgreich aktualisiert!';
} else {
    http_response_code(500);
    echo 'Fehler beim Aktualisieren';
}
$stmt->close();
$mysqli->close(); ?>