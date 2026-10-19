<?php

// 26 FX - unchecked#
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();

$mysqli = utils_connect_sql();

$verteiler = getPostInt('verteiler');
$groupID = getPostInt('groupID');
$ansprechpersonenID = getPostInt('ansprechpersonenID');

if ($groupID > 0 && $ansprechpersonenID > 0) {
	$sql = "UPDATE LIMET_RB.tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen 
            SET Verteiler = ? 
            WHERE tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = ? 
            AND tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen = ?";

	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("iii", $verteiler, $groupID, $ansprechpersonenID);

	if ($stmt->execute()) {
		echo "Verteiler aktualisiert!";
	} else {
		echo "Error: " . $stmt->error;
	}
	$stmt->close();
} else {
	echo "Fehlende Parameter";
}

$mysqli->close();
?>
