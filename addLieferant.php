<?php
if (!function_exists('utils_connect_sql')) {  include "utils/_utils.php"; }
check_login();
if ($_GET["Name"] != "" && $_GET["Vorname"] != "" && $_GET["Tel"] != "") {

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
                                `Gebietsbereich`,
                                `tabelle_abteilung_idtabelle_abteilung`,
                                `tabelle_lieferant_idTABELLE_Lieferant`)
				VALUES
				('" . $_GET["Name"] . "',
				'" . $_GET["Vorname"] . "',
				'" . $_GET["Tel"] . "',
				'" . $_GET["Adresse"] . "',
				'" . $_GET["PLZ"] . "',
				'" . $_GET["Ort"] . "',
				'" . $_GET["Land"] . "',
				'" . $_GET["Email"] . "',
                                '" . $_GET["gebiet"] . "',"
        . $_GET["abteilung"] . ","
        . $_GET["lieferant"] . ");";

    if ($mysqli->query($sql) === TRUE) {
        echo "Kontaktperson hinzugefügt!";
        $id = $mysqli->insert_id;
    } else {
        echo "Error1: " . $sql . "<br>" . $mysqli->error;
    }
    $mysqli->close();
} else {
    echo "Fehler bei der Verbindung";
}
?>
