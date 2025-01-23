<?php
session_start();
$username = $_POST["username"];
$password = $_POST["password"];

if (empty($password)) {
    header('Location: index.php');
    exit(); // Stop further execution
}

$password = md5($password);

$mysqli = new mysqli('localhost', $username, $password, 'LIMET_RB');

if ($mysqli->connect_error) {
    header('Location: index.php');
    $mysqli->close();;
    exit(); // Stop further execution
} else {
    $_SESSION["username"] = $username;
    $_SESSION["password"] = $password;

    $stmt = $mysqli->prepare("SELECT permission FROM tabelle_user_permission WHERE user = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $row = $result->fetch_assoc();
    $mysqli->close();
    if ($row) {
        $_SESSION["ext"] = ($row["permission"] == "1") ? 1 : 0;
        header("Location: projects.php");
        exit(); // Stop further execution
    } else {
        header('Location: index.php');
        exit(); // Stop further execution
    }
}

