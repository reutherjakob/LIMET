<?php

//require_once "../LOGIN/utils.php";
//initPageUser();
header('Content-Type: application/json');
require_once "../LOGIN/db.php";

$roomId = filter_input(INPUT_POST, 'raumid', FILTER_VALIDATE_INT);
if (!$roomId) {
    echo json_encode(['success' => false, 'message' => 'Invalid Room ID']);
    exit;
}

// Assuming all other form data is in POST, except 'raumid'
$formData = $_POST;
unset($formData['raumid']);

// Start transaction
$mysqli->begin_transaction();

// Delete old data for this room
$stmtDelete = $mysqli->prepare("DELETE FROM your_table WHERE raumid = ?");
$stmtDelete->bind_param("i", $roomId);
if (!$stmtDelete->execute()) {
    $mysqli->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to delete previous data']);
    exit;
}
$stmtDelete->close();

// Insert new data
$stmtInsert = $mysqli->prepare("INSERT INTO your_table (raumid, fieldname, fieldvalue) VALUES (?, ?, ?)");

if (!$stmtInsert) {
    $mysqli->rollback();
    echo json_encode(['success' => false, 'message' => 'Prepare insert statement failed']);
    exit;
}

foreach ($formData as $fieldName => $fieldValue) {
    // Allow null for empty values
    $paramValue = strlen($fieldValue) ? $fieldValue : null;
    $stmtInsert->bind_param("iss", $roomId, $fieldName, $paramValue);
    if (!$stmtInsert->execute()) {
        $mysqli->rollback();
        echo json_encode(['success' => false, 'message' => "Failed to save field $fieldName"]);
        exit;
    }
}
$stmtInsert->close();

$mysqli->commit();
$mysqli->close();

echo json_encode(['success' => true, 'message' => 'Data saved successfully']);
