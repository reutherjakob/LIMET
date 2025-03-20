<?php

session_start();
if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
include 'pdf_createBericht_utils.php';


$Änderungsdatum = getValidatedDateFromURL();
//echo $Änderungsdatum;
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_projekt_elementparameter_aenderungen.idtabelle_projekt_elementparameter_aenderungen, tabelle_projekt_elementparameter_aenderungen.projekt, tabelle_projekt_elementparameter_aenderungen.element, tabelle_projekt_elementparameter_aenderungen.parameter, tabelle_projekt_elementparameter_aenderungen.variante, tabelle_projekt_elementparameter_aenderungen.wert_alt, tabelle_projekt_elementparameter_aenderungen.wert_neu, tabelle_projekt_elementparameter_aenderungen.einheit_alt, tabelle_projekt_elementparameter_aenderungen.einheit_neu, tabelle_projekt_elementparameter_aenderungen.timestamp, tabelle_projekt_elementparameter_aenderungen.user
        FROM tabelle_projekt_elementparameter_aenderungen
        WHERE (((tabelle_projekt_elementparameter_aenderungen.projekt)=" . $_SESSION["projectID"] . "))
        AND tabelle_projekt_elementparameter_aenderungen.timestamp > '$Änderungsdatum'
        ORDER BY tabelle_projekt_elementparameter_aenderungen.timestamp DESC;";
$changes = $mysqli->query($sql);
$mysqli->close();

$data = array();
while ($row = $changes->fetch_assoc()) {
    $data[] = $row;
}

$data = filter_old_equal_new($data);
header('Content-Type: application/json'); 
echo json_encode($data, JSON_PRETTY_PRINT);
