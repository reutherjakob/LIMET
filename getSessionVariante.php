<?php

if (!function_exists('utils_connect_sql')) {  include "_utils.php"; }
check_login();

echo $_SESSION["variantenID"];
