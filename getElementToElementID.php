<?php
// 25 FX
require_once "utils/_utils.php";
check_login();

$elementID = getPostInt('elementID');
$_SESSION["elementID"] = $elementID;
$mysqli = utils_connect_sql();

$stmt = $mysqli->prepare("SELECT `Bezeichnung`, `ElementID` FROM `tabelle_elemente` WHERE `idTABELLE_Elemente` = ?");
if (!$stmt) {
    echo "Vorbereitungsfehler: " . htmlspecialchars($mysqli->error);
    exit;
}

$stmt->bind_param("i", $elementID);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
    echo htmlspecialchars($row["ElementID"]) . " " . htmlspecialchars($row["Bezeichnung"]);
} else {
    echo "Element nicht gefunden.";
}

$stmt->close();
$mysqli->close();
?>
