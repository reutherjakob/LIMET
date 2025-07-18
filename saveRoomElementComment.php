<?php
// 4 new Popovers
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$sql = "UPDATE `LIMET_RB`.`tabelle_räume_has_tabelle_elemente`
        SET     `Kurzbeschreibung` = '" . br2nl($_GET["comment"]) . "',
                `Timestamp` = '" . date("Y-m-d H:i:s") . "'
        WHERE   `id` = " . $_GET["id"] . ";"; //id von tabelleRäumeHasElement

if ($mysqli->query($sql) === TRUE) {
    echo "Kommentar erfolgreich aktualisiert!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();
