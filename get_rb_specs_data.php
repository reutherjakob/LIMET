<?php

session_start();

$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
}

if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

$sql = " SELECT
tabelle_funktionsteilstellen.Nummer, 
tabelle_funktionsteilstellen.Bezeichnung,
tabelle_räume.*
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

