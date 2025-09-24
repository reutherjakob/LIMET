<!DOCTYPE html>
<html>
<head>
    <title>Login/Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css"
          integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="container mt-5">
<div class="row">
    <div class="col-md-3"> </div>

    <!--div class="col-md-6">
        <h2>Register</h2>
        <form id="registerForm">
            <div class="mb-3">
                <input class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input class="form-control" name="password" type="password" placeholder="Password" required>
            </div>
            <button class="btn btn-primary">Register</button>
            <div id="registerMsg" class="mt-3"></div>
        </form>
    </div-->
    <div class="col-md-6 card">
        <h2>Login</h2>
        <form id="loginForm">
            <div class="mb-3">
                <input class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input class="form-control" name="password" type="password" placeholder="Password" required>
            </div>
            <button class="btn btn-success">Login</button>
            <div id="loginMsg" class="mt-3"></div>
        </form>
    </div>
</div>
<script>
   // $('#registerForm').submit(function(e) {
   //     e.preventDefault();
   //     $.post('register.php', $(this).serialize(), function(data) {
   //         if (data === "success") {
   //             $('#registerMsg').html('<span class="text-success">Registered! You can log in.</span>');
   //             $('#registerForm')[0].reset();
   //         } else {
   //             $('#registerMsg').html('<span class="text-danger">' + data + '</span>');
   //         }
   //     });
   // });
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        $.post('login.php', $(this).serialize(), function(data) {
            if (data === "success") {
                window.location = 'dashboard.php';
            } else {
                $('#loginMsg').html('<span class="text-danger">' + data + '</span>');
            }
        });
    });
</script>
</body>
</html>