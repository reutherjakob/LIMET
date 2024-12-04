<?php
session_start();
$username = $_POST["username"];
$passwort = $_POST["password"];
if ($passwort === "" || $passwort === null) {
    echo "<script>window.location.href = '/index.php';</script>";
}
$passwort = md5($passwort);

$mysqli = new mysqli('localhost', $username, $passwort, 'LIMET_RB');

if ($mysqli->connect_error) {

    echo "<script>window.location.href = '/index.php';</script>";

} else {
    $_SESSION["username"] = $username;
    $_SESSION["password"] = $passwort;

    $sql = "SELECT permission 
        FROM tabelle_user_permission
        WHERE user='" . $_SESSION["username"] . "';";

    $result = $mysqli->query($sql);

    $row = $result->fetch_assoc();

    if ($row["permission"] == "1") {
        $_SESSION["ext"] = 1;
    } else {
        $_SESSION["ext"] = 0;
    }

    $mysqli->close();
    header("Location: projects.php");
}
