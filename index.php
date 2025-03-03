<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: projects.php");
    exit;
}
?>
<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" /> 
        <title>LIMET - Login</title>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <link rel="stylesheet" href="style.css" type="text/css" media="screen" />
        <link rel="icon" href="iphone_favicon.png"/>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"/>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.css"/>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/b-1.5.2/b-html5-1.5.2/sl-1.2.6/datatables.min.js"></script>
        <style>
            .navbar-brand {
                padding: 0px;
            }
            .navbar-brand>img {
                height: 100%;
                width: auto;
            }
        </style>
    </head>
    <body style='height:100%' id='rbBody'>
        <div class='container-fluid' >
            <nav class="navbar navbar-expand-lg bg-light navbar-light">	
                <a class="py-0 navbar-brand" href="#"><img src="LIMET_logo.png" alt="LIMETLOGO" height="40"/></a>                              
            </nav>
            <div class='row' >
                <div class='col-md-12' id='login'>
                    <form class='form col-md-2' action='login.php' method='post'>
                        <label for='username'>Login</label>
                        <input class='form-control form-control-sm' type='text' id='username' name='username'></input>
                        <label for='password'>Passwort</label>
                        <input class='form-control form-control-sm' type='password' id='password' name='password'></input>
                        <input type='submit' class='btn mt-4 btn-default btn-md' value='Login'></input>
                    </form>
                </div>
            </div>	
        </div>
    </body>
</html>


