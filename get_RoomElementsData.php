<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$roomID = getPostInt('roomID');
$mysqli = utils_connect_sql();
$sql = "SELECT tabelle_räume_has_tabelle_elemente.id,
       tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete, 
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente, 
       tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten,
       tabelle_räume_has_tabelle_elemente.Anzahl, 
       tabelle_elemente.ElementID, 
       tabelle_elemente.Kurzbeschreibung As `Elementbeschreibung`, 
       tabelle_varianten.Variante,
       tabelle_elemente.Bezeichnung,
       tabelle_geraete.GeraeteID, 
       tabelle_hersteller.Hersteller, 
       tabelle_geraete.Typ,
       tabelle_räume_has_tabelle_elemente.`Neu/Bestand`, 
       tabelle_räume_has_tabelle_elemente.Standort, 
       tabelle_räume_has_tabelle_elemente.Verwendung,
       tabelle_räume_has_tabelle_elemente.Kurzbeschreibung, 
       tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente,
       tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete
            FROM tabelle_varianten INNER JOIN (tabelle_hersteller RIGHT JOIN ((tabelle_räume_has_tabelle_elemente LEFT JOIN tabelle_geraete ON tabelle_räume_has_tabelle_elemente.TABELLE_Geraete_idTABELLE_Geraete = tabelle_geraete.idTABELLE_Geraete) INNER JOIN tabelle_elemente ON tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente = tabelle_elemente.idTABELLE_Elemente) ON tabelle_hersteller.idtabelle_hersteller = tabelle_geraete.tabelle_hersteller_idtabelle_hersteller) ON tabelle_varianten.idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten
            WHERE tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume=? AND  tabelle_räume_has_tabelle_elemente.Anzahl <> 0 
            ORDER BY tabelle_elemente.ElementID;";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $roomID);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$mysqli->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
