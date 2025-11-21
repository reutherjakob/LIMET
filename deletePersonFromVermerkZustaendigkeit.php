<?php
//25FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

// Sanitize inputs using the project's POST utility functions
$groupID = getPostInt('groupID');
$ansprechpersonenID = getPostInt('ansprechpersonenID');
$vermerkID = getPostInt('vermerkID');

if ($groupID === 0 || $ansprechpersonenID === 0 || $vermerkID === 0) {
    echo "Invalid input parameters.";
    $mysqli->close();
    exit;
}

// Prepare and execute DELETE from tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen
$stmt1 = $mysqli->prepare("
    DELETE FROM `LIMET_RB`.`tabelle_Vermerkgruppe_has_tabelle_ansprechpersonen`
    WHERE `tabelle_Vermerkgruppe_idtabelle_Vermerkgruppe` = ?
      AND `tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen` = ?
");
if ($stmt1 === false) {
    echo "Error preparing statement 1: " . $mysqli->error;
    $mysqli->close();
    exit;
}
$stmt1->bind_param("ii", $groupID, $ansprechpersonenID);
if (!$stmt1->execute()) {
    echo "Error deleting group-person relation: " . $stmt1->error;
    $stmt1->close();
    $mysqli->close();
    exit;
}
$stmt1->close();

// Prepare and execute DELETE from tabelle_Vermerke_has_tabelle_ansprechpersonen
$stmt2 = $mysqli->prepare("
    DELETE FROM `LIMET_RB`.`tabelle_Vermerke_has_tabelle_ansprechpersonen`
    WHERE `tabelle_Vermerke_idtabelle_Vermerke` = ?
      AND `tabelle_ansprechpersonen_idTABELLE_Ansprechpersonen` = ?
");
if ($stmt2 === false) {
    echo "Error preparing statement 2: " . $mysqli->error;
    $mysqli->close();
    exit;
}
$stmt2->bind_param("ii", $vermerkID, $ansprechpersonenID);

if ($stmt2->execute()) {
    echo "Zustaendigkeit entfernt!";
} else {
    echo "Error deleting vermerk-person relation: " . $stmt2->error;
}

$stmt2->close();
$mysqli->close();
?>
