<?php

session_start();
include '_utils.php';

if (isset($_GET["value"])) {
    $value = filter_var($_GET["value"], FILTER_SANITIZE_STRING);
}

$stmt= "SELECT tabelle_projekte.Projektname, tabelle_räume.*, tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, tabelle_funktionsteilstellen.Nummer, tabelle_funktionsteilstellen.Bezeichnung
FROM tabelle_funktionsteilstellen INNER JOIN (tabelle_räume INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte) ON tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen = tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen
WHERE (((tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen)=" . $value . "))"; 

$mysqli = utils_connect_sql();
$result = $mysqli->query($stmt);
$mysqli->close();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

//echorow($data); 
header('Content-Type: application/json');
echo json_encode($data);

