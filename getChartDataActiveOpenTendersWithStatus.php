<?php
// 25 FX
header('Content-Type: application/json');
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, tabelle_lose_extern.Vergabe_abgeschlossen AS Status, Count(tabelle_lose_extern.Vergabe_abgeschlossen) AS Counter, tabelle_projekte.idTABELLE_Projekte
            FROM tabelle_lose_extern INNER JOIN tabelle_projekte ON tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte = tabelle_projekte.idTABELLE_Projekte
            WHERE ((Not (tabelle_lose_extern.Vergabe_abgeschlossen)=1) AND ((tabelle_projekte.Aktiv)=1))
            GROUP BY tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, tabelle_lose_extern.Vergabe_abgeschlossen
            ORDER BY tabelle_projekte.Interne_Nr, tabelle_projekte.Projektname, Status;";
$result = $mysqli->query($sql);
if (!$mysqli->query($sql)) {
    echo("Error description: " . $mysqli->error);
}

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$result->close();
$mysqli->close();
print json_encode($data);
