<?php
session_start();
include '_utils.php';

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
//$stmt = " SELECT COLUMN_NAME AS ColumnName, DATA_TYPE AS DataType, CHARACTER_MAXIMUM_LENGTH AS CharacterLength
//FROM INFORMATION_SCHEMA.COLUMNS
//WHERE TABLE_NAME = 'tabelle_räume'"; 

// view_Raeume_has_Elemente
//$stmt = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'LIMET_RB'"; 

$stmt= "SELECT tabelle_projekte.Projektname, tabelle_räume.*, tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen, tabelle_funktionsteilstellen.Nummer, tabelle_funktionsteilstellen.Bezeichnung
FROM tabelle_funktionsteilstellen INNER JOIN (tabelle_räume INNER JOIN tabelle_projekte ON tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte) ON tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen = tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen
WHERE (tabelle_räume.Raumbezeichnung) LIKE '%CT%' "; 

 
//$stmt = "SELECT tabelle_räume.tabelle_projekte_idTABELLE_Projekte, tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie, tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte, tabelle_räume.Raumnr, 
//    tabelle_räume.Raumbezeichnung, tabelle_elemente.ElementID, tabelle_varianten.Variante,
//    tabelle_elemente.Bezeichnung as el_Bez, tabelle_parameter.Bezeichnung, tabelle_projekt_elementparameter.Wert, tabelle_projekt_elementparameter.Einheit
//FROM (tabelle_projekt_elementparameter INNER JOIN (tabelle_varianten INNER JOIN (tabelle_räume INNER JOIN (tabelle_elemente INNER JOIN tabelle_räume_has_tabelle_elemente ON tabelle_elemente.idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente) ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) ON (tabelle_projekt_elementparameter.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_elementparameter.tabelle_elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente)) INNER JOIN tabelle_parameter ON tabelle_projekt_elementparameter.tabelle_parameter_idTABELLE_Parameter = tabelle_parameter.idTABELLE_Parameter
//WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") AND "
//        . "((tabelle_parameter.TABELLE_Parameter_Kategorie_idTABELLE_Parameter_Kategorie)=18) AND "
//        . "((tabelle_projekt_elementparameter.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))";
 

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