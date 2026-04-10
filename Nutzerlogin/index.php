<!DOCTYPE html>
<html lang="de">
<head>
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
          crossorigin="anonymous"/>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I"
            crossorigin="anonymous"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
            crossorigin="anonymous"></script>


    <link rel="stylesheet" href="../css/style.css" type="text/css" media="screen"/>
    <link rel="icon" href="../Logo/iphone_favicon.png"/>

</head>
<body>
<div class="container-fluid bg-light" style="height:100vh;">
    <div class='row d-flex align-items-center'>
        <div class='col-xxl-5 col-xl-5 col-lg-4 col-md-2 col-sm-1'></div>
        <div class='col-xxl-2 col-xl-2 col-lg-4 col-md-8 col-sm-10' id='login'>
            <div class='card my-4'>

                <div class='card-header d-flex align-items-center justify-content-center'>
                    <img src="../Logo/LIMET_logo.png" alt="LIMETLOGO">
                </div>

                <form id="loginForm" autocomplete="off" method="POST" class="form">
                    <div class="card-body">
                        <div class='row'>

                            <div class="mb-3 col-xxl-12">
                                <label class="visually-hidden" for="username"> </label>
                                <input class="form-control form-control-lg" name="username" placeholder="Username"
                                       required
                                       autocomplete="off"
                                id="username">

                            </div>

                            <div class="mb-3 col-12">
                                <label class="visually-hidden" for="password"></label>
                                <input class="form-control form-control-lg" name="password" type="password"
                                       placeholder="Password"
                                       required
                                       autocomplete="off" id="password">

                            </div>
                        </div>
                        <!-- DSGVO Einwilligung Checkbox -->
                        <!--div-- class="mb-3 col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="consentCheckbox" name="consentCheckbox" required>
                                <label class="form-check-label" for="consentCheckbox">
                                    Ich habe die <a href="../Nutzerumfrage/DSGVO_Nutzerumfrage.php" target="_blank" rel="noopener">Datenschutzerklärung</a>
                                    gelesen und stimme der Verarbeitung meiner personenbezogenen Daten zu.
                                </label>
                            </div>
                        </div-->

                        <div id="loginMsg" class="mt-3"></div>

                    </div>
                    <div class='card-footer'>
                        <div class='d-flex align-items-center justify-content-center'>
                            <div class='col-3'></div>
                            <button class=" col-6 btn btn-success">Login Nutzende</button>
                            <div class='col-3'></div>
                        </div>

                    </div>
                    <input type="hidden" name="csrf"
                           value="<?php
                           require_once "_utils.php";
                           start_session();
                           require 'csrf.php';
                           echo csrf_token(); ?>">
                    <input type="hidden" name="hashed_password" id="hashed_password"/>
                </form>

            </div>
            <div class='col-xxl-5 col-xl-5 col-lg-4 col-md-2 col-sm-1'></div>
        </div>
    </div>
</body>

<?php
include '../Nutzerlogin/footer.html';
?>

<script>
   // const consentCheckbox = $('#consentCheckbox');
   // const loginBtn = $('#loginBtn');
   // // Login-Button nur aktivieren wenn Checkbox angehakt
   // consentCheckbox.change(function() {
   //     loginBtn.prop('disabled', !this.checked);
   // });

    $('#loginForm').submit(async function (e) {
        e.preventDefault();
        const passwordInput = this.querySelector('input[name="password"]');
       // const hashedPasswordInput = this.querySelector('#hashed_password');
       // hashedPasswordInput.value = await hashPassword(passwordInput.value);
        // passwordInput.value = ''; // optional, clear plain password field

        $.post('login.php', $(this).serialize(), function (data) {
            if (data.status === 'success') {
                window.location = 'forward.php';
            } else if (data.status === 'change_pw') {
                window.location = 'change_pw.php';
            } else {
                // Fehlermeldung anzeigen
                $('#loginMsg').html('<span class="text-danger">' + data.msg + '</span>');
                // CSRF-Token im Formular aktualisieren
                $('input[name="csrf"]').val(data.csrf);
            }
        }, 'json');
    });

</script>

</html>