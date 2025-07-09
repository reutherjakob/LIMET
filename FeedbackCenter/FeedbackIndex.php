<?php
if (!function_exists('utils_connect_sql')) {
    include "../utils/_utils.php";
}
check_login();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'FeedbackController.php';

$action = $_GET['action'] ?? 'index';
$controller = new FeedbackController();

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    $controller->index();
}

