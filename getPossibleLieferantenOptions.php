<?php

require_once 'utils/_utils.php';
check_login();

$deviceID = isset($_SESSION['deviceID']) ? intval($_SESSION['deviceID']) : 0;

if ($deviceID > 0) {
    if (!isset($mysqli) || !$mysqli instanceof mysqli) {
        $mysqli = utils_connect_sql();
    }
    $sql = "SELECT tabelle_lieferant.idTABELLE_Lieferant, 
       tabelle_lieferant.Lieferant, tabelle_lieferant.Land, 
       tabelle_lieferant.Ort
            FROM tabelle_lieferant 
            WHERE tabelle_lieferant.idTABELLE_Lieferant NOT IN (
                SELECT tabelle_lieferant_idTABELLE_Lieferant
                FROM tabelle_geraete_has_tabelle_lieferant
                WHERE tabelle_geraete_idTABELLE_Geraete = ?
            )
            ORDER BY tabelle_lieferant.Lieferant;";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $deviceID);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' .  ($row["idTABELLE_Lieferant"]) . '">'
            .  ($row["Lieferant"]) . ' - ' .  ($row["Land"]) . ' ' . htmlspecialchars($row["Ort"]) . '</option>';
    }

    $stmt->close();

} else {
    echo '<option value="0">Keine Lieferanten verfügbar</option>';
}
