<?php
// toggle_darkmode.php

//session_start();
//$_SESSION['darkmode'] = !($_SESSION['darkmode'] ?? false);
//header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
//exit;
//

session_start();
$_SESSION['darkmode'] = !($_SESSION['darkmode'] ?? false);
// Kein Redirect mehr — einfach OK zurückgeben
http_response_code(200);