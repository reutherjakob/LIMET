<?php

// 26 FX - unchecked#
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();

$mysqli = utils_connect_sql();

$vermerkID = getPostInt('vermerkID');
$vermerkStatus = getPostString('vermerkStatus', "");
$vermerkStatus = trim($vermerkStatus ?? "");

if (!$vermerkID) {
    echo "Ungültige Eingaben.";
    $mysqli->close();
    exit;
}

$stmt = $mysqli->prepare("
    UPDATE `LIMET_RB`.`tabelle_Vermerke`
    SET `Bearbeitungsstatus` = ?
    WHERE `idtabelle_Vermerke` = ?
");

$stmt->bind_param("si", $vermerkStatus, $vermerkID);

if ($stmt->execute()) {
    echo "Vermerk aktualisiert!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
