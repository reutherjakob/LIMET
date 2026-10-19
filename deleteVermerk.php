<?php
// 26 FX - unchecked#
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();

$mysqli = utils_connect_sql();

$vermerkID = getPostInt('vermerkID');
if ($vermerkID>0) {
    $stmt = $mysqli->prepare("DELETE FROM `LIMET_RB`.`tabelle_Vermerke` WHERE `idtabelle_Vermerke` = ?");
    $stmt->bind_param("i", $vermerkID);

    if ($stmt->execute()) {
        echo "Vermerk gelöscht!";
    } else {
        echo "Fehler beim Löschen: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Ungültige oder fehlende Vermerk-ID.";
}

$mysqli->close();

?>
