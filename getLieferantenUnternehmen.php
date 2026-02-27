<?php
require_once "utils/_utils.php";
check_login();
header("Content-Type: application/json; charset=utf-8");

$mysqli = utils_connect_sql();

$sql = "SELECT 
            idTABELLE_Lieferant,
            Lieferant,
            Tel, 
            Anschrift,
            PLZ,
            Ort,
            Land
        FROM tabelle_lieferant
        ORDER BY Lieferant";

$result = $mysqli->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
$mysqli->close();
?>
