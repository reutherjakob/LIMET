<?php

// 26 FX - unchecked#
require_once '_utils/_utils.php';
check_login();
require_once '_utils/_csrf.php';
require_csrf();


$mysqli = utils_connect_sql();

$id = getPostInt('id');
$field = getPostString('field');
$value = getPostString('value');

$sql = "UPDATE tabelle_räume_has_tabelle_elemente SET ? = ? WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sii", $field, $value, $id);

if ($stmt->execute()) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>
