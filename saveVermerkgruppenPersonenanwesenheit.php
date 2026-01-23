<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();


$anwesenheit        = getPostInt('anwesenheit',0);
$groupID            = getPostInt('groupID');
$ansprechpersonenID = getPostInt('ansprechpersonenID');

if ($groupID <= 0 || $ansprechpersonenID <= 0) {
    echo "Ungültige Eingabe.";
    $mysqli->close();
    exit;
}

$sql = "UPDATE tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen
        SET Anwesenheit = ?
        WHERE tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe = ?
          AND tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen = ?";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo "Prepare failed: " . $mysqli->error;
    $mysqli->close();
    exit;
}

$stmt->bind_param("iii", $anwesenheit, $groupID, $ansprechpersonenID);

if ($stmt->execute()) {
    echo "Anwesenheit aktualisiert!";
} else {
    echo "Fehler: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
