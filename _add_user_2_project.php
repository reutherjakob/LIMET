<?php
session_start();
include '_utils.php';
check_login();
$mysqli = utils_connect_sql();

// Prepare and bind
$stmt = $mysqli->prepare("INSERT INTO tabelle_users_have_projects (User, tabelle_projekte_idTABELLE_Projekte) VALUES (?, ?)");
$stmt->bind_param("si", $user, $project_id);

$user = "Weiland";  
$project_ids = [43,51,53,54,57,58,62,66];  // Example array of project IDs

foreach ($project_ids as $project_id) {
    $stmt->execute();
echo "New records created successfully: ". $user .";".$project_id ."! </br> " ;
}


$stmt->close();
$mysqli->close();
?>
