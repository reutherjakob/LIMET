<?php
require_once "utils/_utils.php";
check_login();

$mysqli = utils_connect_sql();

$vermerkID = filter_input(INPUT_GET, 'vermerkID', FILTER_VALIDATE_INT);

if ($vermerkID) {
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
