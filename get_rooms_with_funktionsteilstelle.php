<?php

session_start();
require_once 'utils/_utils.php';

$value = getPostInt('value');
$bool_unique = getPostString('Unique') === "true";


$stmt = "SELECT 
    tabelle_projekte.Projektname, 
    tabelle_räume.*, 
    tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, 
    tabelle_funktionsteilstellen.Nummer, 
    tabelle_funktionsteilstellen.Bezeichnung
FROM tabelle_funktionsteilstellen 
INNER JOIN (
    tabelle_räume 
    INNER JOIN tabelle_projekte 
    ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
) 
ON tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen = tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen
WHERE (tabelle_räume.`MT-relevant` = 1 AND tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen = ?)";

$mysqli = utils_connect_sql();

$stmt_prepared = $mysqli->prepare($stmt);
$stmt_prepared->bind_param("i", $value);
$stmt_prepared->execute();
$result = $stmt_prepared->get_result();

$stmt_prepared->close();
$mysqli->close();

$data = array();

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

if (!$bool_unique) {
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
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