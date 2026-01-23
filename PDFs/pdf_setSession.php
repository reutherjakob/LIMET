<?php
require_once '../utils/_utils.php';
check_login();

if (isset($_POST["PDFdatum"]) && $_POST["PDFdatum"] != "") {
    $_SESSION["PDFdatum"] = $_POST["PDFdatum"];
    echo "PDF Datum erfolgreich gesetzt:". $_SESSION["PDFdatum"];
}

