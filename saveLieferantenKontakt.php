<?php
include "_utils.php";
check_login();

if ($_GET["Name"] != "" && $_GET["Vorname"] != "" && $_GET["Tel"] != "") {


    $mysqli =utils_connect_sql();
    $sql = "UPDATE `LIMET_RB`.`tabelle_ansprechpersonen`
                        SET
                        `Name` = '" . $_GET["Name"] . "',
                        `Vorname` = '" . $_GET["Vorname"] . "',
                        `Tel` = '" . $_GET["Tel"] . "',
                        `Adresse` = '" . $_GET["Adresse"] . "',
                        `PLZ` = '" . $_GET["PLZ"] . "',
                        `Ort` = '" . $_GET["Ort"] . "',
                        `Land` = '" . $_GET["Land"] . "',
                        `Mail` = '" . $_GET["Email"] . "',
                        `Gebietsbereich` = '" . $_GET["gebiet"] . "',
                        `tabelle_abteilung_idtabelle_abteilung` = " . $_GET["abteilung"] . ",
                        `tabelle_lieferant_idTABELLE_Lieferant` = " . $_GET["lieferant"] . "
                        WHERE `idTABELLE_Ansprechpersonen` = " . $_GET["ansprechID"] . ";";

    if ($mysqli->query($sql) === TRUE) {
        echo "Kontaktperson gespeichert!";
        $id = $mysqli->insert_id;
    } else {
        echo "Error1: " . $sql . "<br>" . $mysqli->error;
    }

    $mysqli->close();
} else {
    echo "Fehler bei der Übertragung der Parameter";
}
