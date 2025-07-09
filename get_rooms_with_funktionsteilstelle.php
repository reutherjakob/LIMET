<?php

session_start();
if (!function_exists('utils_connect_sql')) {  include "utils/_utils.php"; }

if (isset($_GET["value"])) {
    $value = filter_var($_GET["value"], FILTER_SANITIZE_STRING);
}

if (isset($_GET["Unique"])) {
    $bool_unique = $_GET["Unique"]=== "true";
}

$stmt = "SELECT tabelle_projekte.Projektname, tabelle_räume.*, 
       tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, tabelle_funktionsteilstellen.Nummer, tabelle_funktionsteilstellen.Bezeichnung
FROM tabelle_funktionsteilstellen INNER JOIN (tabelle_räume INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte) ON tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen = tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen
WHERE (   tabelle_räume.`MT-relevant` = 1  AND tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen=" . $value . ")";
// tabelle_räume.idTABELLE_Räume <>  ". $RaumID." AND

$mysqli = utils_connect_sql();
$result = $mysqli->query($stmt);
$mysqli->close();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
if (!$bool_unique) {
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
// Remove duplicates
    $unique_data = [];
    $seen = [];

    foreach ($data as $item) {
        $key = $item['TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen'] . '|' . $item['Raumbezeichnung'];
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $unique_data[] = $item;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($unique_data);
}