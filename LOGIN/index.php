<!DOCTYPE html>
<html lang="de">
<head>
    <title>Login</title>
    <meta http-equiv="Content-Security-Policy"
          content="default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; script-src 'self' https://code.jquery.com https://cdn.jsdelivr.net">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="container mt-5">
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2>Login</h2>
        <form id="loginForm" autocomplete="off" method="POST">
            <div class="mb-3">
                <input class="form-control" name="username" placeholder="Username" required autocomplete="off">
            </div>
            <div class="mb-3">
                <input class="form-control" name="password" type="password" placeholder="Password" required
                       autocomplete="off">
            </div>
            <input type="hidden" name="csrf" value="<?php session_start();
            require 'csrf.php';
            echo csrf_token(); ?>">
            <button class="btn btn-success">Login</button>
            <div id="loginMsg" class="mt-3"></div>
        </form>
    </div>
</div>
<script>
    async function hashPassword(password) {
        const encoder = new TextEncoder();
        const data = encoder.encode(password);
        const hashBuffer = await crypto.subtle.digest('SHA-256', data);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    }

    $('#loginForm').submit(async function (e) {
        e.preventDefault();

        const passwordInput = this.querySelector('input[name="password"]');
        passwordInput.value = await hashPassword(passwordInput.value);

        $.post('login.php', $(this).serialize(), function (data) {
            if (data === "success") {
                window.location = '../Nutzerumfrage/dashboard.php';
            } else if (data === "change_pw") {
                window.location = 'change_pw.php';
            } else {
                $('#loginMsg').html('<span class="text-danger">Login failed. Please try again.</span>');
            }
        });
    });
</script>

</body>
</html>