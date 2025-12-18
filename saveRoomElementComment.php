<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$id = getPostInt('id');
$comment = getPostString('comment');

$stmt = $mysqli->prepare("UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente` SET `Kurzbeschreibung` = ?, `Timestamp` = ? WHERE `id` = ?");
$date = date("Y-m-d H:i:s");
$br2nl = br2nl($comment);
$stmt->bind_param("ssi", $br2nl, $date, $id);

if ($stmt->execute()) {
    echo "Kommentar erfolgreich aktualisiert!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
