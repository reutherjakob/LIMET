<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">
<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <title>LIMET - Raumbuch - Logout</title>
    <link rel="icon" href="iphone_favicon.png"></link>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php
session_start();
$_SESSION = array();
session_destroy();
?>


<div class='container-fluid bg-light' style="height:100vh;">
    <div class='row d-flex'>
        <div class='col-xxl-5 col-xl-4 col-md-3'></div>
        <div class='col-xxl-2 col-xl-4 col-md-6' id='login'>
            <div class='card me-4 ms-4 mt-4'>
                <div class='card-header d-flex align-items-center justify-content-center'>
                    <img src="LIMET_logo.png" alt="LIMETLOGO">
                </div>
                <div class='card-body d-flex align-items-center justify-content-center'>
                    <p class="text-md-center fs-4 " >  Logout <br> erfolgreich! <br></p>
                </div>
                <div class='card-footer'>
                    <div class='d-flex align-items-center justify-content-center'>
                        <a class="btn btn-lg btn-outline-success" href="index.php" role="button">Login</a>
                    </div>
                </div>
            </div>
        </div>
        <div class='col-xxl-5'></div>
    </div>
</div>
</body>
</html>
