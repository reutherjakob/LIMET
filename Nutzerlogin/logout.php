<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <title>LIMET - Raumbuch - Logout</title>
    <link rel="icon" href="../Logo/iphone_favicon.png"></link>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: index.php");
?>

