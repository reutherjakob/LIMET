<?php
session_start();
include '_utils.php';

if (isset($_GET["value"])) {
    $value = filter_var($_GET["value"], FILTER_SANITIZE_STRING);
}


$stmt =" SELECT Count(tabelle_räume_has_tabelle_elemente.Anzahl) AS AnzahlvonAnzahl, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_projekte.Projektname
FROM tabelle_projekte INNER JOIN (tabelle_elemente INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte
WHERE (((tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen)=" . $value . "))
GROUP BY tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_projekte.Projektname"; 

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


