<?php
// UNUSED
if (!function_exists('utils_connect_sql')) {  include "utils/_utils.php"; }
init_page_serversides("x", ".");
$mysqli = utils_connect_sql();

$vermerkId = $_POST['vermerkId'];
$newContent = $_POST['newContent'];

$stmt = $mysqli->prepare("UPDATE tabelle_Vermerke SET Vermerktext = ? WHERE idtabelle_Vermerke = ?");
$stmt->bind_param("si", $newContent, $vermerkId);

if ($stmt->execute()) {
    echo "Success";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();

