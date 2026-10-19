<?php

// 26 FX - unchecked#
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();

$mysqli = utils_connect_sql();

$schlussgerechnet = getPostInt('schlussgerechnet');
$lotId            = getPostInt('lotID');

$stmt = $mysqli->prepare("
    UPDATE `LIMET_RB`.`tabelle_lose_extern`
    SET `Schlussgerechnet` = ?
    WHERE `idtabelle_Lose_Extern` = ?
");
$stmt->bind_param("ii", $schlussgerechnet, $lotId);

if ($stmt->execute()) {
    $ausgabe = "Gewerk erfolgreich aktualisiert! \n";
} else {
    $ausgabe = "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
echo $ausgabe;
