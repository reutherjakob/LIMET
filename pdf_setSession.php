<?php
if (!function_exists('utils_connect_sql')) {
    include "_utils.php";
}
check_login();

if (isset($_GET["PDFdatum"]) && $_GET["PDFdatum"] != "") {
    $_SESSION["PDFdatum"] = $_GET["PDFdatum"];
}

// $_SESSION["PDFTITEL"]
// $_SESSION["PDFHeaderSubtext"]