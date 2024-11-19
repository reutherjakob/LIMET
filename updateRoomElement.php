<?php
include '_utils.php';
$mysqli = utils_connect_sql();

$id = $_POST['id'];
$field = $_POST['field'];
$value = $_POST['value'];

// Sanitize input
$id = $mysqli->real_escape_string($id);
$field = $mysqli->real_escape_string($field);
$value = $mysqli->real_escape_string($value);

$sql = "UPDATE tabelle_räume_has_tabelle_elemente SET $field = '$value' WHERE id = $id";
if ($mysqli->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $mysqli->error;
}

$mysqli->close();
?>
<?php
