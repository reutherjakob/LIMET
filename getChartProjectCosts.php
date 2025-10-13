<?php
header('Content-Type: application/json');
require_once "utils/_utils.php";
check_login();

$mysqli = utils_connect_sql();
mysqli_query($mysqli, "SET NAMES 'utf8'");

//output array
$data = array();
for ($year = date("Y") - 1; $year <= date("Y"); $year++) {
    for ($month = 1; $month <= 12; $month++) {
        $date = $year . "-" . $month . "-31";

        $sqlAbfrage = "SELECT SUM(CASE WHEN costs.bestand = '0' THEN costs.PP ELSE 0 END) AS Bestand,
                                SUM(CASE WHEN costs.bestand = '1' THEN costs.PP ELSE 0 END) AS Neu
                                FROM
                                (
                                        SELECT rbchanges5.id, rbchanges5.anzahlNeu, rbchanges5.bestand, rbchanges5.elementID, tabelle_elemente.ElementID as eID, tabelle_elemente.Bezeichnung, rbchanges5.variante, rbchanges5.EP, rbchanges5.PP, rbchanges5.idTABELLE_Räume, rbchanges5.Raumnr, rbchanges5.Raumbezeichnung, rbchanges5.raumbereich
                                        FROM 
                                        (	
                                                SELECT rbchanges4.id, rbchanges4.anzahlNeu, rbchanges4.bestand, rbchanges4.elementID, rbchanges4.variante, variantenCosts2.costs AS EP, variantenCosts2.costs*rbchanges4.anzahlNeu AS PP, rbchanges4.idTABELLE_Räume, rbchanges4.Raumnr, rbchanges4.Raumbezeichnung, rbchanges4.raumbereich
                                                FROM 
                                                        (
                                                                SELECT rbchanges3.idTABELLE_Räume, raumaenderungen3.Raumnr, raumaenderungen3.Raumbezeichnung, raumaenderungen3.`Raumbereich Nutzer` AS raumbereich,
                                                                                rbchanges3.id, rbchanges3.tsmax, rbchanges3.aenderung_idmax, rbchanges3.elementID, rbchanges3.anzahlNeu, rbchanges3.bestand, rbchanges3.standort, rbchanges3.variante
                                                                FROM 
                                                                (
                                                                        SELECT tabelle_räume.idTABELLE_Räume, tabelle_räume.Raumnr, tabelle_räume.Raumbezeichnung, tabelle_räume.`Raumbereich Nutzer`
                                                                        FROM tabelle_räume
                                                                )AS raumaenderungen3
                                                                INNER JOIN
                                                                (
                                                                        SELECT rbchanges2.id, rbchanges2.tsmax, rbchanges2.aenderung_idmax, tabelle_rb_aenderung.`elementID_neu` AS elementID, tabelle_rb_aenderung.`Anzahl_copy1` AS anzahlNeu, tabelle_rb_aenderung.`Neu/Bestand_copy1` AS bestand, tabelle_rb_aenderung.Standort_copy1 AS standort, tabelle_rb_aenderung.tabelle_Varianten_idtabelle_Varianten_copy1 As variante, rbchanges2.idTABELLE_Räume
                                                                        FROM tabelle_rb_aenderung
                                                                        INNER JOIN(
                                                                                SELECT rbchanges.id, MAX(rbchanges.ts) AS tsmax, MAX(rbchanges.aenderung_id) AS aenderung_idmax, rbchanges.idTABELLE_Räume
                                                                                FROM
                                                                                        (SELECT tabelle_rb_aenderung.idtabelle_rb_aenderung as aenderung_id, tabelle_rb_aenderung.id AS id, tabelle_rb_aenderung.Timestamp As ts, tabelle_räume.idTABELLE_Räume
                                                                                        FROM (tabelle_räume INNER JOIN tabelle_räume_has_tabelle_elemente 
                                                                                            ON tabelle_räume.idTABELLE_Räume = tabelle_räume_has_tabelle_elemente.TABELLE_Räume_idTABELLE_Räume)
                                                                                            INNER JOIN tabelle_rb_aenderung ON tabelle_räume_has_tabelle_elemente.id = tabelle_rb_aenderung.id
                                                                                        WHERE (((tabelle_räume.tabelle_projekte_idTABELLE_Projekte)=" . $_SESSION["projectID"] . ") 
                                                                                        AND ((tabelle_rb_aenderung.Timestamp) < '" . $date . "')) 
                                                                                        And tabelle_räume_has_tabelle_elemente.Standort = 1 
                                                                                        ORDER BY tabelle_rb_aenderung.id) 
                                                                                        AS rbchanges
                                                                                        GROUP BY rbchanges.id
                                                                                ) AS rbchanges2
                                                                        ON (tabelle_rb_aenderung.idtabelle_rb_aenderung = rbchanges2.aenderung_idmax)
                                                                )
                                                                AS rbchanges3
                                                                ON raumaenderungen3.idTABELLE_Räume = rbchanges3.idTABELLE_Räume
                                                        )
                                                AS rbchanges4
                                                LEFT JOIN
                                                        (
                                                                SELECT variantenCosts.id, variantenCosts.elID, variantenCosts.elVar, variantenCosts.tsCosts, `tabelle_projekt_varianten_kosten_aenderung`.`kosten_neu` AS costs
                                                                FROM `tabelle_projekt_varianten_kosten_aenderung`
                                                                INNER JOIN
                                                                        (
                                                                                SELECT 
                                                                                        MAX(`tabelle_projekt_varianten_kosten_aenderung`.`idtabelle_projekt_varianten_kosten_aenderung`) AS id,
                                                                                        `tabelle_projekt_varianten_kosten_aenderung`.`element` AS elID,
                                                                                        `tabelle_projekt_varianten_kosten_aenderung`.`variante` AS elVar,
                                                                                        MAX(`tabelle_projekt_varianten_kosten_aenderung`.`timestamp` ) AS tsCosts
                                                                                FROM `tabelle_projekt_varianten_kosten_aenderung`
                                                                                WHERE `tabelle_projekt_varianten_kosten_aenderung`.`projekt` = " . $_SESSION["projectID"] . " AND
                                                                                                `tabelle_projekt_varianten_kosten_aenderung`.`geraet` IS NULL AND
                                                                                                `tabelle_projekt_varianten_kosten_aenderung`.`timestamp` < '" . $date . "'
                                                                                GROUP BY elID, elVar
                                                                        ) AS variantenCosts
                                                                ON (variantenCosts.id = `tabelle_projekt_varianten_kosten_aenderung`.`idtabelle_projekt_varianten_kosten_aenderung`)
                                                        )AS variantenCosts2
                                                ON (rbchanges4.elementID = variantenCosts2.elID AND rbchanges4.variante = variantenCosts2.elVar)
                                        )AS rbchanges5
                                        INNER JOIN
                                        tabelle_elemente
                                        ON tabelle_elemente.idTABELLE_Elemente = rbchanges5.elementID
                                        ORDER BY EP, tabelle_elemente.ElementID
                                )AS costs;";
        //execute query
        $result = $mysqli->query($sqlAbfrage);

        //loop through the returned data
        while ($row = $result->fetch_assoc()) {
            $data[] = array($date, $row['Bestand'], $row['Neu']);
        }
    }
}

//free memory associated with result
$result->close();

//close connection
$mysqli->close();

//now print the data
print json_encode($data);
//echo $data;
?>			
