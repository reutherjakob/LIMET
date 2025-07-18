<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();


$sql = "INSERT INTO `LIMET_RB`.`tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen`
                (`tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe`,
                `tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen`)
                VALUES
                (" . filter_input(INPUT_GET, 'groupID') . ",
                " . filter_input(INPUT_GET, 'ansprechpersonenID') . ");";

if ($mysqli->query($sql) === TRUE) {
    echo "Teilnehmer hinzugefügt!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();

?>
