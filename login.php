<?php
 
session_start();

$username = $_POST["username"];
$password = $_POST["password"];

if (empty($password)) {
    echo "<script>window.location.href = '/index.php';</script>";
    exit();
}

$password = md5($password);

$mysqli = new mysqli('localhost', $username, $password, 'LIMET_RB');

if ($mysqli->connect_error) {
    echo "<script>window.location.href = '/index.php';</script>";
    exit();
}
$_SESSION["username"] = $username;
$_SESSION["password"] = $password; // Keeping the password in the session as requested

$stmt = $mysqli->prepare("SELECT permission FROM tabelle_user_permission WHERE user = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {
   $_SESSION["ext"] = ($row["permission"] == "1") ? 1 : 0;
    header("Location: projects.php");
} else {
    echo "<script>window.location.href = '/index.php';</script>";
}

$stmt->close();
$mysqli->close();
