<?php
if (!function_exists('utils_connect_sql')) {
    include "utils/_utils.php";
}
check_login();

$mysqli = utils_connect_sql();

// Sanitize and validate inputs
$projectID   = intval($_SESSION["projectID"]);
$elementID   = intval($_SESSION["elementID"]);
$parameterID = intval($_GET["parameterID"]);
$variantenID = intval($_GET["variantenID"]);
$wert    = $_GET["wert"] ?? '';
$einheit = $_GET["einheit"] ?? '';


// Use prepared statements for security
$sql = "UPDATE `LIMET_RB`.`tabelle_projekt_elementparameter`
        SET `Wert` = ?, `Einheit` = ?
        WHERE `tabelle_projekte_idTABELLE_Projekte` = ?
        AND `tabelle_elemente_idTABELLE_Elemente` = ?
        AND `tabelle_parameter_idTABELLE_Parameter` = ?
        AND `tabelle_Varianten_idtabelle_Varianten` = ?";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $mysqli->error);
}

$stmt->bind_param("ssiiii", $wert, $einheit, $projectID, $elementID, $parameterID, $variantenID);
$success = $stmt->execute();

if ($success) {
    // Fetch parameter Bezeichnung
    $sqlBez = "SELECT `Bezeichnung` FROM `tabelle_parameter` WHERE `idTABELLE_Parameter` = ?";
    $stmtBez = $mysqli->prepare($sqlBez);
    $stmtBez->bind_param("i", $parameterID);
    $stmtBez->execute();
    $stmtBez->bind_result($bezeichnung);
    $stmtBez->fetch();
    $stmtBez->close();

    echo "Parameter <strong>" . htmlspecialchars($bezeichnung) . "</strong> erfolgreich aktualisiert! " . $wert . " " . $einheit . " " . $projectID . " " . $elementID . " " . $parameterID . " " . $variantenID . " ";
} else {
    echo "Fehler beim Aktualisieren des Parameters: " . $stmt->error;
}



$stmt->close();
$mysqli->close();
