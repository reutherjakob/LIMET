<?php
// 25 FX
header('Content-Type: application/json');
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

$sql = "SELECT tabelle_lose_extern.Vergabe_abgeschlossen, 
               Sum(tabelle_lose_extern.Vergabesumme) AS SummevonVergabesumme,
               Sum(tabelle_lose_extern.Budget) AS SummevonBudget, 
               Sum(tabelle_lose_extern.Vergabesumme-tabelle_lose_extern.Budget) AS Delta
        FROM tabelle_lose_extern
        WHERE (((tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte)=?))
        GROUP BY tabelle_lose_extern.Vergabe_abgeschlossen;";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i',$_SESSION['projectID']);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$stmt -> close();
$result->close();
$mysqli->close();
print json_encode($data);

