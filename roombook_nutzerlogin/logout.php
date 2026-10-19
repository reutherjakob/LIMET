<?php
require_once "../roombook_nutzerlogin/_utils.php";
start_session();
$_SESSION = array();
session_destroy();
header("Location: index.php");
exit;
?>