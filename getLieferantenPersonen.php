<?php
require_once "utils/_utils.php";
check_login();
header("Content-Type: application/json; charset=utf-8");

$mysqli = utils_connect_sql();

$sql = "SELECT 
            tabelle_ansprechpersonen.idTABELLE_Ansprechpersonen,
            tabelle_ansprechpersonen.Name,
            tabelle_ansprechpersonen.Vorname, 
            tabelle_ansprechpersonen.Tel,
            tabelle_ansprechpersonen.Adresse,
            tabelle_ansprechpersonen.PLZ,
            tabelle_ansprechpersonen.Ort,
            tabelle_ansprechpersonen.Land,
            tabelle_ansprechpersonen.Mail,
            tabelle_abteilung.Abteilung,
            tabelle_lieferant.Lieferant,
            tabelle_lieferant.idTABELLE_Lieferant, 
            tabelle_abteilung.idtabelle_abteilung,
            tabelle_ansprechpersonen.Gebietsbereich
        FROM 
            tabelle_abteilung
        INNER JOIN (
            tabelle_lieferant
            INNER JOIN tabelle_ansprechpersonen 
                ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_ansprechpersonen.tabelle_lieferant_idTABELLE_Lieferant
        ) 
        ON tabelle_abteilung.idtabelle_abteilung = tabelle_ansprechpersonen.tabelle_abteilung_idtabelle_abteilung
        where Lieferant <> 'Test123' ";

$result = $mysqli->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
