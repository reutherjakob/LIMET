<?php
// 10-2025 FX
require_once 'utils/_utils.php';
check_login();

$id = getPostString('lieferantID');
$firma = getPostString('firma');
$tel = getPostString('lieferantTel');
$adresse = getPostString('lieferantAdresse');
$plz = getPostString('lieferantPLZ');
$ort = getPostString('lieferantOrt');
$land = getPostString('lieferantLand');


$requiredFields = [$firma, $tel, $adresse, $plz, $ort, $land];
if (in_array("", $requiredFields, true)) {
    die("Fehler: Bitte alle Felder ausfüllen!");
}

$mysqli = utils_connect_sql();

if ($id && is_numeric($id)) {
    $stmt = $mysqli->prepare("UPDATE tabelle_lieferant SET 
        Lieferant = ?,
        Tel = ?,
        Anschrift = ?,
        PLZ = ?,
        Ort = ?,
        Land = ?
        WHERE idTABELLE_Lieferant = ?");
    $stmt->bind_param("ssssssi", $firma, $tel, $adresse, $plz, $ort, $land, $id);
} else {
    $stmt = $mysqli->prepare("INSERT INTO tabelle_lieferant 
        (Lieferant, Tel, Anschrift, PLZ, Ort, Land)
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firma, $tel, $adresse, $plz, $ort, $land);
}
if ($stmt->execute()) {
    echo $id ? "Lieferant aktualisiert!" : "Lieferant hinzugefügt!";
} else {
    error_log("Database error: " . $stmt->error);
    echo "Fehler: Operation fehlgeschlagen. Bitte Administrator benachrichtigen.";
}

$stmt->close();
$mysqli->close();
