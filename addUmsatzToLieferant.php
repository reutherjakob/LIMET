<?php
// 25 FX
include "utils/_format.php";
include_once "utils/_utils.php";
check_login();
$mysqli = utils_connect_sql();

$umsatz = getPostFloat('umsatz', -1);
$bereich = getPostString('bereich');
$jahr = getPostInt('jahr', 0);

$lieferantenID = isset($_SESSION["lieferantenID"]) ? intval($_SESSION["lieferantenID"]) : 0;

// Input validation
if ($umsatz < 0) {
    exit("Ungültiger Umsatzwert.");
}
if (empty($bereich) || !preg_match('/^[a-zA-ZäöüÄÖÜß\s]{1,50}$/u', $bereich)) {
    exit("Ungültiger Geschäftsbereich.");
}
if ($jahr < 1900 || $jahr > 2100) {
    exit("Ungültiges Jahr.");
}
if ($lieferantenID <= 0) {
    exit("Ungültige Lieferanten-ID.");
}

// Prepared statement to insert data
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
$mysqli->close();
?>
