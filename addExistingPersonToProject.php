<?php
// 10-2025 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();
$projectID = $_SESSION["projectID"];
$personID = getPostInt('personID');
$zustaendigkeit = getPostInt('zustaendigkeit');
$organisation = getPostInt('organisation');
if (!$personID || !$zustaendigkeit || !$organisation) {
    echo "Ungültige Eingaben!";
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO tabelle_projekte_has_tabelle_ansprechpersonen 
    (TABELLE_Projekte_idTABELLE_Projekte, TABELLE_Ansprechpersonen_idTABELLE_Ansprechpersonen, TABELLE_Projektzuständigkeiten_idTABELLE_Projektzuständigkeiten, tabelle_organisation_idtabelle_organisation) 
    VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $projectID, $personID, $zustaendigkeit, $organisation);


if ($stmt->execute()) {
    echo "Eintrag erfolgreich hinzugefügt!";
} else {
    echo "Fehler: " . $stmt->error;
}
$stmt->close();
$mysqli->close();
?>
