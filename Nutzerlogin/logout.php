<?php
require_once "../Nutzerlogin/_utils.php";
start_session();
$_SESSION = array();
session_destroy();
header("Location: index.php");
exit;
?>