<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');

/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}
//echo getcwd();

// name of the uploaded file
$filename = $_FILES['fileUpload']['name'];
$idRechnung = filter_input(INPUT_POST, 'rechnungIDFile');


// destination of the file on the server
$destination = "/var/www/vhosts/limet-rb.com/httpdocs/Dokumente_RB/Rechnungen/Rechnung_" . $idRechnung . ".pdf";

// get the file extension
$extension = pathinfo($filename, PATHINFO_EXTENSION);

// the physical file on a temporary uploads directory on the server
$size = $_FILES['fileUpload']['size'];

if (!in_array($extension, ['pdf'])) {
    echo "Dokument muss .pdf sein!";
} elseif ($_FILES['fileUpload']['size'] > 1000000) { // file shouldn't be larger than 1Megabyte
    echo "Datei zu groß! Max. 1 MB!";
} else {
    // Abfrage ob bereits Datei/File zu Rechnung hinterlegt
    $sql = "SELECT tabelle_rechnungen.idtabelle_rechnungen, tabelle_rechnungen.tabelle_Files_idtabelle_Files
                FROM tabelle_rechnungen
                WHERE tabelle_rechnungen.idtabelle_rechnungen=" . filter_input(INPUT_POST, 'rechnungIDFile') . ";";

    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    if ($row["tabelle_Files_idtabelle_Files"] != null) {
        //Datei vorhanden -> Ersetzen!
        // move the uploaded (temporary) file to the specified destination
        if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], $destination)) {
            echo " Datei erfolgreich hochgeladen \n";

            $sql_Update = "UPDATE `LIMET_RB`.`tabelle_Files`
                            SET
                            `Timestamp` = now()
                            WHERE
                            `idtabelle_Files` = " . $row["tabelle_Files_idtabelle_Files"] . ";";

            if ($mysqli->query($sql_Update) === TRUE) {
                echo " Datei ersetzt!";
            } else {
                echo " Error: " . $mysqli->error;
            }
        } else {
            echo " Fehler beim Ersatz-Upload!";
        }
    } else {
        // move the uploaded (temporary) file to the specified destination
        if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], $destination)) {
            echo " Datei erfolgreich hochgeladen \n";

            $sql_insert = "INSERT INTO `LIMET_RB`.`tabelle_Files`
                            (`tabelle_projekte_idTABELLE_Projekte`,
                            `Type`,
                            `Timestamp`,
                            `Name`)
                            VALUES
                            (" . $_SESSION["projectID"] . ",
                            'Rechnung',
                            now(),
                            'Rechnung_" . $idRechnung . ".pdf');";

            if (mysqli_query($mysqli, $sql_insert)) {
                $id = $mysqli->insert_id;
                $sql_Update = "UPDATE `LIMET_RB`.`tabelle_rechnungen`
                                    SET
                                    `tabelle_Files_idtabelle_Files` = " . $id . "
                                    WHERE `idtabelle_rechnungen` = " . filter_input(INPUT_POST, 'rechnungIDFile') . ";";

                if ($mysqli->query($sql_Update) === TRUE) {
                    echo " Rechnung um Datei ergänzt!";
                } else {
                    echo " Error: " . $mysqli->error;
                }
            } else {
                echo " Error: " . $mysqli->error;
            }
        } else {
            echo " Fehler beim Upload!";
        }
    }
}

$mysqli->close();
?>