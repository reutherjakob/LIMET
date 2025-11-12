<?php
require_once 'utils/_utils.php';
check_login();

$deviceID = isset($_SESSION["deviceID"]) ? intval($_SESSION["deviceID"]) : 0;

if ($deviceID > 0) {

    if (!isset($mysqli) || !$mysqli instanceof mysqli) {
        $mysqli = utils_connect_sql();
    }

    $sql = "SELECT tabelle_lieferant.Lieferant, 
                tabelle_lieferant.idTABELLE_Lieferant
            FROM tabelle_lieferant
            INNER JOIN tabelle_geraete_has_tabelle_lieferant
            ON tabelle_lieferant.idTABELLE_Lieferant = tabelle_geraete_has_tabelle_lieferant.tabelle_lieferant_idTABELLE_Lieferant
            WHERE tabelle_geraete_has_tabelle_lieferant.tabelle_geraete_idTABELLE_Geraete = ?
            ORDER BY tabelle_lieferant.Lieferant;";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $deviceID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row["idTABELLE_Lieferant"]) . '">' .
            htmlspecialchars($row["Lieferant"]) . '</option>';
    }

    $stmt->close();


}
?>
