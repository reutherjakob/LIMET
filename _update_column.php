<?php

session_start();
include '_utils.php';
check_login();


echo "".$_SESSION['projectID'].""; 
$mysqli = utils_connect_sql();

//$stmt = $mysqli->prepare("UPDATE tabelle_räume SET Bauabschnitt = 'F' 
//                              WHERE tabelle_projekte_idTABELLE_Projekte = ? 
//                              AND (Bauabschnitt LIKE '%Haus F%' OR Bauabschnitt IN ('O', 'M', 'N', 'W'))");
$stmt = $mysqli->prepare("UPDATE tabelle_räume SET Geschoss = 'OG1' 
                              WHERE tabelle_projekte_idTABELLE_Projekte = ? 
                              AND `Raumbereich Nutzer` LIKE 'Chirurgische Tagesklinik'");

$BBE = 80; 
$KHI = 75; 
$stmt->bind_param('i', $KHI);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Column updated successfully.";
} else {
    echo "No rows updated.";
}

$stmt->close();
$mysqli->close();

?>
