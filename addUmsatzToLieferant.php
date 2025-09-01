<?php
include "utils/_format.php";
include_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();
// Validate and sanitize inputs
$umsatz = filter_input(INPUT_GET, 'umsatz', FILTER_VALIDATE_FLOAT);
$bereich_raw = filter_input(INPUT_GET, 'bereich', FILTER_UNSAFE_RAW);
$bereich = trim($bereich_raw);
$jahr = filter_input(INPUT_GET, 'jahr', FILTER_VALIDATE_INT);

$lieferantenID = isset($_SESSION["lieferantenID"]) ? intval($_SESSION["lieferantenID"]) : 0;

// Simple input checks
if ($umsatz === false || $umsatz < 0) {
    exit("Ungültiger Umsatzwert.");
}
if (empty($bereich) || !preg_match('/^[a-zA-ZäöüÄÖÜß\s]{1,50}$/u', $bereich)) {
    exit("Ungültiger Geschäftsbereich.");
}
if ($jahr === false || $jahr < 1900 || $jahr > 2100) {
    exit("Ungültiges Jahr.");
}
if ($lieferantenID <= 0) {
    exit("Ungültige Lieferanten-ID.");
}

// Prepare statement to prevent SQL injection
$stmt = $mysqli->prepare("INSERT INTO LIMET_RB.tabelle_umsaetze (umsatz, geschaeftsbereich, jahr, tabelle_lieferant_idTABELLE_Lieferant) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    exit("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("dsii", $umsatz, $bereich, $jahr, $lieferantenID);

if ($stmt->execute()) {
    echo "Umsatz hinzugefügt!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
