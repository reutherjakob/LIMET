<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$id = getPostInt('id');
$comment = getPostString('comment');

$stmt = $mysqli->prepare("UPDATE tabelle_lose_extern SET `Notiz` = ? WHERE idtabelle_Lose_Extern = ?");
$date = date("Y-m-d H:i:s");
$br2nl = br2nl($comment);  //           tabelle_lose_extern.Notiz,
$stmt->bind_param("si",  $br2nl, $id);

if ($stmt->execute()) {
    echo "Kommentar erfolgreich aktualisiert!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
