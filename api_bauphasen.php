<?php
require_once 'utils/_utils.php';
check_login();

header('Content-Type: application/json');

$mysqli = utils_connect_sql();
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    ?? filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$response = ['success' => false, 'message' => ''];

switch ($action) {

    case 'getAll':
        $projektId = filter_input(INPUT_GET, 'projekt_id', FILTER_VALIDATE_INT);
        if (!$projektId) {
            $response['message'] = 'Keine Projekt-ID angegeben.';
            break;
        }
        $sql = "SELECT * FROM tabelle_bauphasen WHERE tabelle_projekte_idTABELLE_Projekte = ? ORDER BY datum_beginn ASC";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('i', $projektId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $response['success'] = true;
        $response['data'] = $data;
        break;


    case 'add':
        $bauphase          = filter_input(INPUT_POST, 'bauphase', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $datumBeginn       = filter_input(INPUT_POST, 'datum_beginn', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $datumFertig       = filter_input(INPUT_POST, 'datum_fertigstellung', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $projektId         = filter_input(INPUT_POST, 'projekt_id', FILTER_VALIDATE_INT);

        if (!$bauphase || !$datumBeginn || !$datumFertig || !$projektId) {
            $response['message'] = 'Alle Felder sind Pflichtfelder.';
            break;
        }
        if ($datumFertig < $datumBeginn) {
            $response['message'] = 'Fertigstellungsdatum darf nicht vor dem Beginndatum liegen.';
            break;
        }

        $sql = "INSERT INTO tabelle_bauphasen (bauphase, datum_beginn, datum_fertigstellung, tabelle_projekte_idTABELLE_Projekte)
                VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('sssi', $bauphase, $datumBeginn, $datumFertig, $projektId);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Bauphase erfolgreich hinzugefügt.';
            $response['id'] = $mysqli->insert_id;
        } else {
            $response['message'] = 'Fehler beim Hinzufügen: ' . $mysqli->error;
        }
        break;

    case 'update':
        $id          = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $bauphase    = filter_input(INPUT_POST, 'bauphase', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $datumBeginn = filter_input(INPUT_POST, 'datum_beginn', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $datumFertig = filter_input(INPUT_POST, 'datum_fertigstellung', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!$id || !$bauphase || !$datumBeginn || !$datumFertig) {
            $response['message'] = 'Alle Felder sind Pflichtfelder.';
            break;
        }
        if ($datumFertig < $datumBeginn) {
            $response['message'] = 'Fertigstellungsdatum darf nicht vor dem Beginndatum liegen.';
            break;
        }

        $sql = "UPDATE tabelle_bauphasen SET bauphase = ?, datum_beginn = ?, datum_fertigstellung = ? WHERE idtabelle_bauphasen = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('sssi', $bauphase, $datumBeginn, $datumFertig, $id);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Bauphase erfolgreich aktualisiert.';
        } else {
            $response['message'] = 'Fehler beim Aktualisieren: ' . $mysqli->error;
        }
        break;

    case 'delete':
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            $response['message'] = 'Ungültige ID.';
            break;
        }
        $sql = "DELETE FROM tabelle_bauphasen WHERE idtabelle_bauphasen = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Bauphase erfolgreich gelöscht.';
        } else {
            $response['message'] = 'Fehler beim Löschen: ' . $mysqli->error;
        }
        break;

    default:
        $response['message'] = 'Unbekannte Aktion.';
        break;
}

$mysqli->close();
echo json_encode($response);