<?php
//
require_once 'utils/_utils.php';

$TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen =  getPostInt('value',0);

if($TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen == 0){die("Kein Wert übergeben.");}

$stmt = "SELECT COUNT(tabelle_räume_has_tabelle_elemente.Anzahl) AS AnzahlvonAnzahl,
       SUM(tabelle_räume_has_tabelle_elemente.Anzahl) AS SummevonAnzahl,
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
       tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_projekte.Projektname
        FROM tabelle_projekte INNER JOIN (tabelle_elemente INNER JOIN (tabelle_räume_has_tabelle_elemente 
        INNER JOIN tabelle_räume ON tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume = 
        tabelle_räume.idTABELLE_Räume ) ON tabelle_elemente.idTABELLE_Elemente =
        tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente ) 
        ON tabelle_projekte.idTABELLE_Projekte = tabelle_räume.tabelle_projekte_idTABELLE_Projekte 
        WHERE tabelle_räume.TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen = ? 
        GROUP BY tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
                 tabelle_elemente.ElementID, tabelle_elemente.Bezeichnung, tabelle_projekte.Projektname";

$mysqli = utils_connect_sql();
$stmt_prepared = $mysqli->prepare($stmt);
$stmt_prepared->bind_param("i", $TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen);
$stmt_prepared->execute();
$result = $stmt_prepared->get_result();
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
$stmt_prepared->close();
$mysqli->close();
header('Content-Type: application/json');
echo json_encode($data);