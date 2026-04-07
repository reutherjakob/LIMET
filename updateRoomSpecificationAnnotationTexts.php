<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();
$mysqli = utils_connect_sql();

$stmt = $mysqli->prepare("UPDATE tabelle_räume SET 
    `Anmerkung FunktionBO` = ?, `Anmerkung Geräte` = ?, 
    `Anmerkung BauStatik` = ?, `Anmerkung Elektro` = ?, 
    `Anmerkung MedGas` = ?, `Anmerkung HKLS` = ? 
    WHERE idTABELLE_Räume = ?");

$getPostString = getPostString("funktionBO");
$getPostString1 = getPostString("geraete");
$getPostString2 = getPostString("baustatik");
$getPostString3 = getPostString("Elektro");
$getPostString4 = getPostString("medgas");
$getPostString5 = getPostString("hkls");
$stmt->bind_param("ssssssi",
    $getPostString,
    $getPostString1,
    $getPostString2,
    $getPostString3,
    $getPostString4,
    $getPostString5,
    $_SESSION["roomID"]);

if ($stmt->execute()) {
    echo "Raum erfolgreich aktualisiert!";
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
