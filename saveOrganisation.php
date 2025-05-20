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

// Check if organisation already exists (optional)
$stmt = $mysqli->prepare("SELECT idtabelle_organisation FROM tabelle_organisation WHERE Organisation = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$stmt->bind_result($existingId);
if ($stmt->fetch()) {
    echo json_encode(['success' => true, 'id' => $existingId, 'name' => $name]);
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

