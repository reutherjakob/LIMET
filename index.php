<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="de">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <title>LIMET - Login</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen"/>
    <link rel="icon" href="iphone_favicon.png"/>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>

</head>

<body id='rbBody'>

<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: projects.php");
    exit;
}
?>

<div class='container-fluid bg-light' style="height:100vh;">
    <div class='row d-flex'>
        <div class='col-5'></div>
        <div class='col-2' id='login'>
            <div class='card me-4 ms-4 mt-4'>
                <div class='card-header d-flex align-items-center justify-content-center'>
                    <img src="LIMET_logo.png" alt="LIMETLOGO">
                </div>
                <div class=''>
                    <form class='form' action='login.php' method='post'>
                        <div class='card-body'>
                            <div class='row '>
                                <div class='col-12 mt-4'>
                                    <label for='username' class="visually-hidden">Username</label>
                                    <input class='form-control form-control-lg' type='text' id='username'
                                           name='username'
                                           placeholder="Username">
                                </div>
                                <div class='col-12 mt-4'>
                                    <label for='password' class="visually-hidden">Passwort</label>
                                    <input class='form-control form-control-lg' type='password' id='password'
                                           name='password'
                                           placeholder="Passwort">
                                </div>
                                <div class='col-12 mt-4'></div>
                            </div>
                        </div>
                        <div class='card-footer'>
                            <div class='d-flex align-items-center justify-content-center'>
                                <input type='submit' class='btn btn-success' value='Login'>
                            </div>
                        </div>

                    </form>
                </div>


            </div>
        </div>
    </div>
    <div class='col-5'></div>
</div>

</body>
</html>


