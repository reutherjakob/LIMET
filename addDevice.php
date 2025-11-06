<?php
// 10-2025 FX
require_once 'utils/_utils.php';
check_login();

$mysqli = utils_connect_sql();

// Validate and get POST inputs
$type = getPostString('type');
$kurzbeschreibung = getPostString('kurzbeschreibung');
$hersteller = getPostInt('hersteller', 0);
$elementIDSession = $_SESSION["elementID"] ?? 0;

if (empty($type) || empty($kurzbeschreibung) || $hersteller === 0 || $elementIDSession === 0) {
    echo "Ungültige Eingaben!";
    exit;
}

// Get ElementID
$stmt = $mysqli->prepare("SELECT ElementID FROM tabelle_elemente WHERE idTABELLE_Elemente = ?");
$stmt->bind_param("i", $elementIDSession);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    echo "Element nicht gefunden!";
    exit;
}

$elementID = $row["ElementID"];


$stmt = $mysqli->prepare("SELECT MAX(Laufende_Nr) AS maxLaufendeNr FROM tabelle_geraete WHERE TABELLE_Elemente_idTABELLE_Elemente = ?");
$stmt->bind_param("i", $elementIDSession);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$laufendeNr = ($row && $row['maxLaufendeNr'] !== null) ? ((int)$row['maxLaufendeNr'] + 1) : 1;

// Prepare insert statement
$geraeteID = $elementID . "." . $laufendeNr;
$datum = date('Y-m-d');

$stmt = $mysqli->prepare("INSERT INTO tabelle_geraete (GeraeteID, Typ, Kurzbeschreibung, Änderung, TABELLE_Elemente_idTABELLE_Elemente, Laufende_Nr, tabelle_hersteller_idtabelle_hersteller) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssiii", $geraeteID, $type, $kurzbeschreibung, $datum, $elementIDSession, $laufendeNr, $hersteller);

if ($stmt->execute()) {
    echo "Gerät hinzugefügt! " . $datum;
} else {
    echo "Fehler: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
