<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$budgetId   = getPostInt('budgetID');
$roombookId = getPostInt('roombookID');

if ($budgetId === 0) {
	$stmt = $mysqli->prepare("
        UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
        SET `tabelle_projektbudgets_idtabelle_projektbudgets` = NULL
        WHERE `id` = ?
    ");
	$stmt->bind_param("i", $roombookId);
} else {
	$stmt = $mysqli->prepare("
        UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
        SET `tabelle_projektbudgets_idtabelle_projektbudgets` = ?
        WHERE `id` = ?
    ");
	$stmt->bind_param("ii", $budgetId, $roombookId);
}

if ($stmt->execute()) {
	echo "Erfolgreich aktualisiert!";
} else {
	echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
