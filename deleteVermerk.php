<?php
session_start();
?>

<?php
if (!isset($_SESSION["username"])) {
    echo "Bitte erst <a href=\"index.php\">einloggen</a>";
    exit;
}
?>

<?php
$mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
if (!$mysqli->set_charset("utf8")) {
    printf("Error loading character set utf8: %s\n", $mysqli->error);
    exit();
}

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (filter_input(INPUT_GET, 'vermerkID') != '') {
    $sql = "DELETE FROM `LIMET_RB`.`tabelle_Vermerke`
                    WHERE `idtabelle_Vermerke`=" . filter_input(INPUT_GET, 'vermerkID') . ";";
}

if ($mysqli->query($sql) === TRUE) {
    echo "Vermerk gelöscht!";
} else {
    echo "Error: " . $sql . "<br>" . $mysqli->error;
}

$mysqli->close();
?>
