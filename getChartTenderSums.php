<?php
// 2025-11- FX
header('Content-Type: application/json');
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();
$projectID = isset($_SESSION["projectID"]) ? intval($_SESSION["projectID"]) : 0;


$sql = "SELECT SUM(tabelle_lose_extern.Vergabesumme) AS SummevonVergabesumme, tabelle_lieferant.Lieferant
        FROM tabelle_lieferant 
        INNER JOIN tabelle_lose_extern 
        ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_lose_extern.tabelle_lieferant_idTABELLE_Lieferant
        WHERE tabelle_lose_extern.tabelle_projekte_idTABELLE_Projekte = ?
        GROUP BY tabelle_lieferant.Lieferant
        ORDER BY SummevonVergabesumme";

$stmt = $mysqli->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    $mysqli->close();
    print json_encode($data);
} else {
    // handle error appropriately, e.g., log and output a safe message
    $mysqli->close();
    echo json_encode(["error" => "Database query failed"]);
}

?>			
