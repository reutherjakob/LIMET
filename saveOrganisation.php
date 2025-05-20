<?php

if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();
$mysqli = utils_connect_sql();

$name = trim($_POST['name'] ?? '');

if ($name === '') {
    echo json_encode(['success' => false, 'error' => 'Kein Name angegeben.']);
    exit;
}

// Prepare lower-case name for comparison
$lowerName = mb_strtolower($name, 'UTF-8');

// Check if organisation already exists (case-insensitive)
$stmt = $mysqli->prepare("SELECT idtabelle_organisation, Organisation FROM tabelle_organisation WHERE LOWER(Organisation) = ?");
$stmt->bind_param("s", $lowerName);
$stmt->execute();
$stmt->bind_result($existingId, $existingName);
if ($stmt->fetch()) {
    // Return the existing name from DB to avoid case discrepancies
    echo json_encode(['success' => true, 'id' => $existingId, 'name' => $existingName]);
    exit;
}
$stmt->close();

// Insert new organisation
$stmt = $mysqli->prepare("INSERT INTO tabelle_organisation (Organisation) VALUES (?)");
$stmt->bind_param("s", $name);
if ($stmt->execute()) {
    $newId = $stmt->insert_id;
    echo json_encode(['success' => true, 'id' => $newId, 'name' => $name]);
} else {
    echo json_encode(['success' => false, 'error' => 'Fehler beim Einfügen.']);
}
$stmt->close();
$mysqli->close();
?>
