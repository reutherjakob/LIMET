<?php
require_once 'utils/_utils.php';
check_login();

if ($_POST["Name"] != "" && $_POST["Vorname"] != "" && $_POST["Tel"] != "") {

    $mysqli = utils_connect_sql();
    $sql = "INSERT INTO `tabelle_ansprechpersonen`
				(`Name`,
				`Vorname`,
				`Tel`,
				`Adresse`,
				`PLZ`,
				`Ort`,
				`Land`,
				`Mail`,
               `Raumnr`)
				VALUES
				('" . $_POST["Name"] . "',
				'" . $_POST["Vorname"] . "',
				'" . $_POST["Tel"] . "',
				'" . $_POST["Adresse"] . "',
				'" . $_POST["PLZ"] . "',
				'" . $_POST["Ort"] . "',
				'" . $_POST["Land"] . "',
				'" . $_POST["Email"] . "',
                '" . $_POST["Raumnr"] . "');";

    if ($mysqli->query($sql) === TRUE) {
        echo "Person angelegt ";
        $id = $mysqli->insert_id;
    } else {
        echo "Error1: " . $sql . "<br>" . $mysqli->error;
    }

    $sql = "INSERT INTO `tabelle_projekte_has_tabelle_ansprechpersonen` (`TABELLE_Projekte_idTABELLE_Projekte`,`TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen`,`TABELLE_Projektzuständigkeiten_idTABELLE_Projektzuständigkeiten`,`tabelle_organisation_idtabelle_organisation`)
				VALUES (" . $_SESSION["projectID"] . "," . $id . "," . $_POST["zustaendigkeit"] . "," . $_POST["organisation"] . ");";

    if ($mysqli->query($sql) === TRUE) {
        echo "und zu Projekt hinzugefügt!";
    } else {
        echo "Error2: " . $sql . "<br>" . $mysqli->error;
    }

    $mysqli->close();
} else {
    echo "Fehler bei der Verbindung";
}
?>
