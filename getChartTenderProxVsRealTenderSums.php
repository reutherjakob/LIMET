<?php
// 11-25-FX
header('Content-Type: application/json');
require_once "utils/_utils.php";
check_login();


$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_lose_extern.idtabelle_Lose_Extern, tabelle_lose_extern.LosNr_Extern, tabelle_lose_extern.LosBezeichnung_Extern, tabelle_lose_extern.Vergabesumme,
		losschaetzsumme.Summe,
		losschaetzsumme.id, 
                        IFNULL(losschaetzsumme.Summe,0) - IFNULL(tabelle_lose_extern.Vergabesumme,0) AS delta
                FROM tabelle_lieferant 
                RIGHT JOIN tabelle_lose_extern 
                ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
                LEFT JOIN
                                (SELECT tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern AS id, Sum(tabelle_räume_has_tabelle_elemente.`Anzahl`*tabelle_projekt_varianten_kosten.`Kosten`) AS Summe
                                FROM tabelle_räume INNER JOIN (tabelle_projekt_varianten_kosten INNER JOIN tabelle_räume_has_tabelle_elemente ON (tabelle_projekt_varianten_kosten.tabelle_Varianten_idtabelle_Varianten = tabelle_räume_has_tabelle_elemente.tabelle_Varianten_idtabelle_Varianten) AND (tabelle_projekt_varianten_kosten.tabelle_elemente_idTABELLE_Elemente = tabelle_räume_has_tabelle_elemente.TABELLE_Elemente_idTABELLE_Elemente)) ON (tabelle_räume.tabelle_projekte_idTABELLE_Projekte = tabelle_projekt_varianten_kosten.tabelle_projekte_idTABELLE_Projekte) AND (tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                                WHERE (((tabelle_räume_has_tabelle_elemente.`Neu/Bestand`)=1) AND ((tabelle_räume_has_tabelle_elemente.Standort)=1) AND ((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                                GROUP BY tabelle_räume_has_tabelle_elemente.tabelle_Lose_Extern_idtabelle_Lose_Extern)
                        AS losschaetzsumme
                        ON (tabelle_lose_extern.idtabelle_Lose_Extern = losschaetzsumme.id)
                        WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . "))
                    ORDER BY tabelle_lose_extern.LosNr_Extern;";


$result = $mysqli->query($sql);
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
$result->close();
$mysqli->close();
print json_encode($data);
?>			
