<?php

// 10-2025 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

$groupID = getPostInt('groupID');
$ansprechpersonenID = getPostInt('ansprechpersonenID');

$stmt = $mysqli->prepare("INSERT INTO `LIMET_RB`.`tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen`
    (`tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe`, `tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen`)
    VALUES (?, ?)");

if ($stmt) {
    $stmt->bind_param('ii', $groupID, $ansprechpersonenID);

    if ($stmt->execute()) {
        echo "Teilnehmer hinzugefügt!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Fehler bei der Vorbereitung: " . $mysqli->error;
}

$mysqli->close();
?>
