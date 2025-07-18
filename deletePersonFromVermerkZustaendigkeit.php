<?php
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$sql = "DELETE FROM `LIMET_RB`.`tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen`
                WHERE `tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe`= " . filter_input(INPUT_GET, 'groupID') . "
                AND `tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen`=" . filter_input(INPUT_GET, 'ansprechpersonenID') . ";";

$sql = "DELETE FROM `LIMET_RB`.`tabelle_Vermerke_has_tabelle_ansprechpersonen`
                WHERE `tabelle_Vermerke_idtabelle_Vermerke`= " . filter_input(INPUT_GET, 'vermerkID') . " AND `tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen`=" . filter_input(INPUT_GET, 'ansprechpersonenID') . ";";

if ($mysqli->query($sql) === TRUE) {
    echo "Zustaendigkeit entfernt!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();
?>
