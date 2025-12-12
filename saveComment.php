<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
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