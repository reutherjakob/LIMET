<?php
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

$sql = "INSERT INTO `LIMET_RB`.`tabelle_räume`
                (`Raumnr`,
                `Raumbezeichnung`,
                `TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen`,
                `tabelle_projekte_idTABELLE_Projekte`,
                `Raumbereich Nutzer`,
                `Geschoss`,
                `Bauetappe`,
                `Bauabschnitt`,
                `Nutzfläche`,
                `MT-relevant`)
                VALUES
                ('" . filter_input(INPUT_GET, 'raumnummer') . "',
                '" . filter_input(INPUT_GET, 'raumbezeichnung') . "',
                '" . filter_input(INPUT_GET, 'funktionsteilstelle') . "',
                " . $_SESSION["projectID"] . ",
                '" . filter_input(INPUT_GET, 'raumbereich') . "',
                '" . filter_input(INPUT_GET, 'geschoss') . "',
                '" . filter_input(INPUT_GET, 'bauetappe') . "',
                '" . filter_input(INPUT_GET, 'bauteil') . "',
                '" . filter_input(INPUT_GET, 'nutzflaeche') . "',
                '" . filter_input(INPUT_GET, 'MTrelevant') . "');";

if ($mysqli->query($sql) === TRUE) {
    echo "Raum erfolgreich hinzugefügt!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();


?>
  