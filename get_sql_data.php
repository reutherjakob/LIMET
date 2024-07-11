<?php
session_start();
include '_utils.php';

$mysqli = utils_connect_sql();
$stmt = $mysqli->prepare("
    SELECT*
    FROM tabelle_räume
    INNER JOIN tabelle_funktionsteilstellen ON tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen = tabelle_funktionsteilstellen.idTABELLE_Funktionsteilstellen
    WHERE (tabelle_funktionsteilstellen.Nummer= ?)
    ORDER BY tabelle_räume.Raumnr
                ");  

$passParam= '1.09.00' ; 
$stmt->bind_param("s", $passParam);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echorow($data); 
$mysqli ->close(); 



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