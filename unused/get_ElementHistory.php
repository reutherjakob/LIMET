<?php
// 25 FX
require_once 'utils/_utils.php';
include 'PDFs/_pdf_createBericht_utils.php';
check_login();

$Änderungsdatum = getPostDate("date");
$mysqli = utils_connect_sql();

$sql = "SELECT idtabelle_projekt_elementparameter_aenderungen, projekt, element, parameter, variante, wert_alt, wert_neu, einheit_alt, einheit_neu, timestamp, user FROM tabelle_projekt_elementparameter_aenderungen WHERE projekt = ? AND timestamp > ? ORDER BY timestamp DESC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("is", $_SESSION["projectID"], $Änderungsdatum);
$stmt->execute();
$changes = $stmt->get_result();
$mysqli->close();

$data = array();
while ($row = $changes->fetch_assoc()) {
    $data[] = $row;
}

$data = filter_old_equal_new($data);
header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT);
