<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

if (!empty($_SESSION["deviceID"]) && !empty($_POST["lieferantenID"])) {
	require_once 'utils/_utils.php';
	check_login();

	$deviceID = $_SESSION["deviceID"];
	$lieferantenID = getPostInt('lieferantenID', 0);

	if ($deviceID === 0 || $lieferantenID === 0) {
		echo "Ungültige Eingaben!";
		exit;
	}

	$mysqli = utils_connect_sql();
	$stmt = $mysqli->prepare("INSERT INTO LIMET_RB.tabelle_geraete_has_tabelle_lieferant 
                             (tabelle_geraete_idTABELLE_Geraete, tabelle_lieferant_idTABELLE_Lieferant) 
                             VALUES (?, ?)");
	$stmt->bind_param("ii", $deviceID, $lieferantenID);
	if ($stmt->execute()) {
		echo "Lieferant zu Gerät hinzugefügt!";
	} else {
		echo "Fehler: " . $stmt->error;
	}

	$stmt->close();
	$mysqli->close();

} else {
	echo "Fehler bei der Verbindung";
}
?>
