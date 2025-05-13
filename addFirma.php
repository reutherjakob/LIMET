<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();

// Collect all fields via POST only (more secure than GET)
$id = filter_input(INPUT_POST, 'lieferantID');
$firma = filter_input(INPUT_POST, 'firma');
$tel = filter_input(INPUT_POST, 'lieferantTel');
$adresse = filter_input(INPUT_POST, 'lieferantAdresse');
$plz = filter_input(INPUT_POST, 'lieferantPLZ');
$ort = filter_input(INPUT_POST, 'lieferantOrt');
$land = filter_input(INPUT_POST, 'lieferantLand');

// Validate required fields
$requiredFields = [$firma, $tel, $adresse, $plz, $ort, $land];
if (in_array("", $requiredFields, true)) {
    die("Fehler: Bitte alle Felder ausfüllen!");
}

$mysqli = utils_connect_sql();

if ($id && is_numeric($id)) {
    // UPDATE existing Lieferant using prepared statement
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
    // INSERT new Lieferant using prepared statement
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
?>
