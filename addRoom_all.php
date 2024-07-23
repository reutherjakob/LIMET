<?php
session_start();

if (!isset($_SESSION["username"])) {
    echo "Bitte erst <a href=\"index.php\">einloggen</a>";
    exit;
}

$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

/* change character set to utf8 */
if (!$mysqli->set_charset("utf8")) {
    echo "Error loading character set utf8: " . $mysqli->error;
    exit();
}

// Retrieve all GET parameters
$params = $_GET;

$table = "tabelle_räume";

$columns = [];
$values = [];

foreach ($params as $key => $value) {
    // Substitute the column name if needed
    if ($key === "fk_TABELLE_Räume_TABELLE_Funktionsteilstellen1") {
        $key = "`TABELLE_Funktionsteilstellen_idTABELLE_Funktionsteilstellen`"; // Add backticks
    }
    if($key ==="Bezeichnung" || $key ==="Nummer"){continue; }
    $columns[] = "`$key`";
    $values[] = "'" . $mysqli->real_escape_string($value) . "'";
}

$columnsString = implode(", ", $columns);
$valuesString = implode(", ", $values);
 
$columnsString= str_replace('+', ' ', $columnsString); 

$sql = "INSERT INTO `LIMET_RB`.`$table` ($columnsString) VALUES ($valuesString)";

if ($mysqli->query($sql) === TRUE) {
    echo "Raum erfolgreich hinzugefügt!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}
$mysqli->close();
?> 
