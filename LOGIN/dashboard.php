<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; script-src 'self' https://code.jquery.com https://cdn.jsdelivr.net");

require 'db.php';
$stmt = $mysqli->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// Restrict access
//if ($role >6 ) {
//    echo "Access denied.";
//    exit;
//}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="container">
<h1>Welcome!</h1>
<a href="logout.php" class="btn btn-danger">Logout</a>
</body>
</html>