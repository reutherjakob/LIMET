<?php
session_start();
include '_utils.php';
check_login();
$mysqli = utils_connect_sql();

// Prepare and bind
$stmt = $mysqli->prepare("INSERT INTO tabelle_users_have_projects (User, tabelle_projekte_idTABELLE_Projekte) VALUES (?, ?)");
$stmt->bind_param("si", $user, $project_id);


$user = "fischer";  
$project_id = 75;  
$stmt->execute();

echo "New record created successfully";

$stmt->close();
$mysqli->close();

