<?php

require 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // $stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    //$stmt->bind_param("ss", $username, $hash);

   // if ($stmt->execute()) {
   //     echo "success";
   // } else {
   //     echo "Username already taken.";
   // }
   // exit;
}

 