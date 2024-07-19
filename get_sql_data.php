<?php
session_start();
include '_utils.php';
if (isset($_GET["key"])) {
    $key = filter_var($_GET["key"], FILTER_SANITIZE_STRING);
} else {
    $key = "TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen ";
}
if (isset($_GET["value"])) {
    $value = filter_var($_GET["value"], FILTER_SANITIZE_STRING);
} else {
    $value = "0";
}
if ($key === "TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen") {
    $key = "idTABELLE_Funktionsteilstellen";
}

//echo "Key: ". $key. " Value: ". $value. "<br>"; 

$mysqli = utils_connect_sql();
//$stmt = " SELECT *
//    FROM tabelle_räume
//    INNER JOIN tabelle_funktionsteilstellen ON tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen = tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
//    WHERE  `MT-relevant` =1
//    ORDER BY tabelle_räume.Raumnr";

//$stmt = "SELECT tabelle_räume.*, tabelle_funktionsteilstellen.*
//    FROM tabelle_räume
//    INNER JOIN tabelle_funktionsteilstellen ON tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen = tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
//    WHERE `MT-relevant` = 1
//    ORDER BY tabelle_räume.Raumnr" ;

    
// ALL TABLES and their fields
//$stmt  = "SELECT TABLE_NAME, COLUMN_NAME
//FROM INFORMATION_SCHEMA.COLUMNS
//WHERE TABLE_SCHEMA = 'LIMET_RB'"; 
//
//////INFOS ABOUT A TABLE 
$stmt = " SELECT COLUMN_NAME AS ColumnName, DATA_TYPE AS DataType, CHARACTER_MAXIMUM_LENGTH AS CharacterLength
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'view_Raeume_has_Elemente'"; 
// view_Raeume_has_Elemente

//$stmt = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'LIMET_RB'"; 

$result = $mysqli->query($stmt);
$mysqli->close(); 
 

echorow($result); 
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
 
echorow($data); 
//header('Content-Type: application/json');
//echo json_encode($data);
 
/* 
Aufruf von Bauangaben von Vergleichsräume:
Dazu kannst du die Abfrage der Bauangaben-Neu verwenden und TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen nach dem Wert des aktuellen Raumes suchen
Sollte dem Anwender dazu dienen, dass er einen Vergleich mit anderen Projekten machen kann
vlt automatisierter Vergleich mit Darstellung von Abweichungen?
vlt eigener Modalaufruf?
Brainstorming willkomen 
Aufruf von Elementen im Raum von Vergleichsräumen - dazu zwei mögliche Abfragen:
 
 * SELECT tabelle_räume.idTABELLE_Räume, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
FROM tabelle_projekte INNER JOIN (tabelle_elemente INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte
WHERE (((tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen)=128))
GROUP BY tabelle_räume.idTABELLE_Räume, tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung;

 * SELECT tabelle_räume.idTABELLE_Räume, tabelle_projekte.Interne_Nr,
    tabelle_projekte.Projektname, Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
    tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
FROM tabelle_projekte INNER JOIN (tabelle_elemente INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume 
ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) 
ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) 
ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte
WHERE (((tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen)= ?))
GROUP BY tabelle_räume.idTABELLE_Räume, tabelle_projekte.Interne_Nr, 
tabelle_projekte.Projektname, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung;
 
FROM tabelle_elemente INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has * SELECT Sum(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
WHERE (((tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen)=128))
GROUP BY tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung;

 * SELECT Count(tabelle_räume_has_tabelle_elemente.Anzahl) AS AnzahlvonAnzahl, tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung
FROM tabelle_elemente INNER JOIN (tabelle_räume_has_tabelle_elemente INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = tabelle_räume.idTABELLE_Räume) ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente
WHERE (((tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen)=128))
GROUP BY tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung;
 * 
Evtl. mit Ampelsystem:
Grün - Elemente sind aktuell berücksichtigt
Gelb - Fehlen im aktuellen Projekt
  */