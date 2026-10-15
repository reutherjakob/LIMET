<?php

// 26 FX - unchecked#
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();

$mysqli = utils_connect_sql();

$comment = getPostString('comment');
$amount  = getPostFloat('amount');
$id      = getPostInt('id');

$stmt = $mysqli->prepare("
    UPDATE view_Raeume_has_Elemente
    SET Kurzbeschreibung = ?, Anzahl = ?
    WHERE id = ?
");
$stmt->bind_param("sdi", $comment, $amount, $id);

if ($stmt->execute()) {
	echo "Erfolgreich aktualisiert!";
} else {
	echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();