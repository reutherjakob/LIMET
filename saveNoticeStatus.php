<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$status   = getPostString('status');
$noticeId = isset($_SESSION['noticeID']) ? (int)$_SESSION['noticeID'] : 0;

$stmt = $mysqli->prepare("
    UPDATE `LIMET_RB`.`tabelle_notizen`
    SET `Notiz_bearbeitet` = ?
    WHERE `idtabelle_notizen` = ?
");
$stmt->bind_param("si", $status, $noticeId);

if ($stmt->execute()) {
   echo "Notizstatus erfolgreich aktualisiert!";
} else {
   echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
