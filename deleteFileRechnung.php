<?php
// 25Fx
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

// Sanitize and get idRechnung safely from POST
$idRechnung = getPostInt("idRechnung");

// Destination path of the file on the server
$file_path = "/var/www/vhosts/limet-rb.com/httpdocs/Dokumente_RB/Rechnungen/Rechnung_" . $idRechnung . ".pdf";

$response = ["file_path" => $file_path];

// Delete file if it exists
if (file_exists($file_path)) {
    if (unlink($file_path)) {
        $response["file_deleted"] = "Datei gelöscht!";

        // Prepared statement to get file ID linked to Rechnung
        $stmt = $mysqli->prepare("SELECT idtabelle_rechnungen FROM tabelle_rechnungen WHERE idtabelle_rechnungen = ?");
        $stmt->bind_param("i", $idRechnung);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $idFile = $row["tabelle_Files_idtabelle_Files"];
        $stmt->close();

        // Update the Rechnung to remove the file reference
        $stmt_update = $mysqli->prepare("UPDATE tabelle_rechnungen SET idtabelle_rechnungen = NULL WHERE idtabelle_rechnungen = ?");
        $stmt_update->bind_param("i", $idRechnung);
        if ($stmt_update->execute()) {
            $response["rechnung_update"] = "Datei von Rechnung entfernt!";
        } else {
            $response["error_update"] = $stmt_update->error;
        }
        $stmt_update->close();

        // Delete the file entry from tabelle_Files
        $stmt_delete = $mysqli->prepare("DELETE FROM tabelle_Files WHERE idtabelle_Files = ?");
        $stmt_delete->bind_param("i", $idFile);
        if ($stmt_delete->execute()) {
            $response["file_deleted_db"] = "File gelöscht!";
        } else {
            $response["error_delete"] = $stmt_delete->error;
        }
        $stmt_delete->close();

    } else {
        $response["error_unlink"] = "Datei konnte nicht gelöscht werden!";
    }
} else {
    $response["error_file_exists"] = "Datei existiert nicht!";
}

$mysqli->close();

echo json_encode($response);
?>
