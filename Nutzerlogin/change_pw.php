<?php
global $mysqli;
require 'db.php';

session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Strict'
]);

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$msg = '';

// Load current stored password hash for the user
$stmt = $mysqli->prepare("SELECT password, must_change_pw FROM tabelle_users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($current_hashl,$must_change_pw );
$stmt->fetch();
$stmt->close();
if ($must_change_pw == 0) {
    header("Location: index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pw_hashed = $_POST['hashed_password'] ?? '';

    if (strlen($new_pw_hashed) !== 64 || !ctype_xdigit($new_pw_hashed)) {
        $msg = "Ungültiges Passwort.";
    } elseif ($new_pw_hashed === $current_hash) {
        $msg = "Ungültiges Passwort.";
    } else {
        // Store hashed password directly
        $stmt = $mysqli->prepare("UPDATE tabelle_users SET password = ?, must_change_pw = 0 WHERE id = ?");
        $stmt->bind_param("si", $new_pw_hashed, $_SESSION['user_id']);
        $stmt->execute();
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8"/>
    <title>Passwort ändern</title>
    <link rel="icon" href="../Logo/iphone_favicon.png"/>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"/>

</head>
<body class="container-fluid mt-4">
<div class='row d-flex align-items-center'>
    <div class='col-xxl-5 col-xl-5 col-lg-4 col-md-2 col-sm-1'></div>
    <div class='col-xxl-2 col-xl-2 col-lg-4 col-md-8 col-sm-10' id='login'>
        <div class='card mx-auto my-4' style="outline: 2px solid white;">
            <form method="POST" autocomplete="off" id="changePwForm">
                <div class='card-header d-flex align-items-center justify-content-center'>
                    <img src="../Logo/LIMET_logo.png" alt="LIMETLOGO">

                </div>
                <div class='card-body'>

                    <?php if ($msg): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
                    <?php endif; ?>
                    <div class='row'>
                        <div class='col-xxl-12 mt-4'>
                            <label for="new_pw" class="visually-hidden"></label>
                            <input class="form-control mb-3" name="new_pw" id="new_pw" type="password"
                                   placeholder="Neues Passwort" required autocomplete="off">
                            <input type="hidden" name="hashed_password" id="hashed_password"/>
                            <div class="mt-3">
                                <small>Das Passwort muss mindestens 12 Zeichen lang sein, Groß- und Kleinbuchstaben,
                                    eine Zahl, und ein Sonderzeichen enthalten.</small>
                            </div>
                        </div>

                    </div>
                </div>
                <div class='card-footer'>
                    <button class="btn btn-success col-12">Passwort ändern</button>


                </div>
            </form>
        </div>
    </div>
    <div class='col-xxl-5 col-xl-5 col-lg-4 col-md-2 col-sm-1'></div>
</div>


<script>
    async function hashPassword(password) {
        const encoder = new TextEncoder();
        const data = encoder.encode(password);
        const hashBuffer = await crypto.subtle.digest('SHA-256', data);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    }

    document.getElementById('changePwForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const pwField = this.querySelector('input[name="new_pw"]');
        const hashedField = this.querySelector('#hashed_password');
        hashedField.value = await hashPassword(pwField.value);
        pwField.value = '';
        this.submit();
    });
</script>

</body>
</html>
