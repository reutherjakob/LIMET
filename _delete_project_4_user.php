<?php
session_start();
include '_utils.php';
check_login();
$mysqli = utils_connect_sql();

// Prepare and bind
$stmt = $mysqli->prepare("DELETE FROM tabelle_users_have_projects WHERE User = ? AND tabelle_projekte_idTABELLE_Projekte = ?");
$stmt->bind_param("si", $user, $project_id);

// Set parameters and execute
$user = "xxx";  
$project_id = 43;  
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Row deleted successfully";
} else {
    echo "No matching row found";
}

$stmt->close();
$mysqli->close();

?>
