<?php
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();
$imageID =   getPostInt('imageID', 0 );
//filter_input(INPUT_POST, 'imageID', FILTER_VALIDATE_INT);

if ($imageID === 0) {
    die("Invalid image ID.");
}

// Prepare statement to fetch image name
$stmt = $mysqli->prepare("SELECT `Name` FROM `LIMET_RB`.`tabelle_Files` WHERE `idtabelle_Files` = ?");
$stmt->bind_param('i', $imageID);
$stmt->execute();
$stmt->bind_result($imageName);
if (!$stmt->fetch()) {
    $stmt->close();
    $mysqli->close();
    die("Image not found.");
}
$stmt->close();

// Construct safe path for the image
$baseDir = "/var/www/vhosts/limet-rb.com/httpdocs/Dokumente_RB/Images/";
$target_dir = $baseDir . basename($imageName); // basename prevents directory traversal

// Delete file safely
if (!unlink($target_dir)) {
    echo "Fehler beim Löschen!";
} else {
    echo "Datei gelöscht! ";

    // Prepare delete statement for DB cleanup
    $stmtDelete = $mysqli->prepare("DELETE FROM `LIMET_RB`.`tabelle_Files` WHERE `idtabelle_Files` = ?");
    $stmtDelete->bind_param('i', $imageID);

    if ($stmtDelete->execute()) {
        echo "Datenbank aktualisiert!";
    } else {
        echo "Error: " . $stmtDelete->error;
    }
    $stmtDelete->close();
}

$mysqli->close();

?>
