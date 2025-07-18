<?php
require_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

// get File from POST
$image = $_POST['fileUpload'] ?? null;

// get vermerkID from POST
//$vermerkID = filter_input(INPUT_POST, 'vermerkID');

// validate input and session data
$projectID = $_SESSION["projectID"] ?? null;
if (!$image || !$projectID) {
    http_response_code(400);
    echo "Fehlende Daten!";
    exit;
}

// set directory on SERVER
$target_dir = "/var/www/vhosts/limet-rb.com/httpdocs/Dokumente_RB/Images/Image_" . intval($projectID) . "_";

//replacing some characters from the base64
$image = str_replace('data:image/jpeg;base64,', '', $image);
$image = str_replace(' ', '+', $image);

//decoding the base64
$data = base64_decode($image, true);
if ($data === false) {
    echo "Ungültige Bilddaten!";
    exit;
}

//generating a unique name
$unique = uniqid('', true);
$filename = "Image_" . intval($projectID) . "_" . $unique . ".jpeg";

//setting the path together
$file = $target_dir . $unique . '.jpeg';

//putting all the content into a file
$success = file_put_contents($file, $data);

if ($success !== false) {
    echo " Bild erfolgreich hochgeladen \n";

    // Datenbankeintrag erstellen
    $stmt = $mysqli->prepare("
        INSERT INTO `LIMET_RB`.`tabelle_Files`
        (`tabelle_projekte_idTABELLE_Projekte`,
         `tabelle_filetype_id`,
         `Timestamp`,
         `Name`) 
        VALUES (?, 1, NOW(), ?)
    ");
    $stmt->bind_param("is", $projectID, $filename);

    if ($stmt->execute()) {
        echo " Bild in Datenbank ergänzt!  \n" . $unique . " \n" . $filename;

        /*
        $id = $mysqli->insert_id;

        $sql_insert2 = "INSERT INTO `LIMET_RB`.`tabelle_Files_has_tabelle_Vermerke`
                        (`tabelle_Files_idtabelle_Files`,
                        `tabelle_Vermerke_idtabelle_Vermerke`)
                        VALUES
                        (".$id.",
                         ".$vermerkID.");";

        if ($mysqli->query($sql_insert2) === TRUE) {
            echo " Vermerk um Bilddateu ergänzt!";
        }
        else {
            echo " Error: " .$mysqli->error;
        }*/
    } else {
        echo " Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo " Fehler beim Bild-Upload!";
}

$mysqli->close();
?>
