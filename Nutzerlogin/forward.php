<?php

global $mysqli;
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userid = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT role FROM tabelle_users WHERE id = ?");
$stmt->bind_param('i', $userid);
$stmt->execute();
$stmt->bind_result($role);
if ($stmt->fetch()) {
    $stmt->close();
    switch ($role) {
        case 'spargefeld_admin':
        case 'spargefeld_ext_users':
            header('Location: ../Nutzerumfrage/Nutzerabfrage.php');
            break;
        case 'internal_rb_user':
            header('Location: ../Nutzerumfrage/Nutzerabfrage.php');
//            header('Location: ../Nutzerumfrage/adminpanel.php');
            break;

        default:
            header('Location: default_dashboard.php');
            break;
    }
    exit;
} else {
    $stmt->close();
    header('Location: index.php');
    exit;
}

