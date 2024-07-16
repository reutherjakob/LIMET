<?php

session_start();

$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
} else {
    //echo "Connected successfully";
}

if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

$sql = " SELECT tabelle_räume.tabelle_projekte_idTABELLE_Projekte, 
           tabelle_räume.idTABELLE_Räume, 
           tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, 
           tabelle_funktionsteilstellen.Nummer, 
           tabelle_funktionsteilstellen.Bezeichnung,
           tabelle_räume.`MT-relevant`, 
           tabelle_räume.Raumnr, 
           tabelle_räume.Raumbezeichnung, 
           tabelle_räume.`Funktionelle Raum Nr`, 
           tabelle_räume.Raumnummer_Nutzer, 
           tabelle_räume.`Raumbereich Nutzer`, 
           tabelle_räume.Strahlenanwendung, 
           tabelle_räume.Laseranwendung, 
           tabelle_räume.H6020, 
           tabelle_räume.Anwendungsgruppe
        FROM tabelle_räume
	INNER JOIN tabelle_funktionsteilstellen ON tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen = tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
        ORDER BY tabelle_räume.Raumnr";

if (!$mysqli->query($sql)) {
    echo "Error executing query: " . $mysqli->error;
} else {
    $result = $mysqli->query($sql); 
} 

$mysqli->close();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

 