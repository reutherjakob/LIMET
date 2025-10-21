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
$stmt = $mysqli->prepare("SELECT password, must_change_pw FROM tabelle_users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($current_hash, $must_change_pw);
$stmt->fetch();
$stmt->close();

if ($must_change_pw == 0) {
    header("Location: index.php");
    exit;
}

// Lade Top-Passwortliste in Array
$topPasswords = file('top_passwords.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$topPasswords = array_map('trim', $topPasswords);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pw_plain = $_POST['new_pw'] ?? '';

    // Check Top Passwords
    if (in_array($new_pw_plain, $topPasswords, true)) {
        $msg = "Dieses Passwort ist viel zu leicht zu erraten, bitte wählen Sie ein anderes.";
    }
    // Passwortregeln prüfen
    elseif (strlen($new_pw_plain) < 8 ||
        !preg_match('/[A-Z]/', $new_pw_plain) ||
        !preg_match('/[a-z]/', $new_pw_plain) ||
        !preg_match('/[0-9]/', $new_pw_plain) ||
        !preg_match('/[\W_]/', $new_pw_plain)) {
        $msg = "Das Passwort entspricht nicht den Sicherheitserfordernissen.";
    }
    // Prüfen ob das neue PW gleich dem alten ist
    elseif (password_verify($new_pw_plain, $current_hash)) {
        $msg = "Das neue Passwort darf nicht mit dem aktuellen Passwort übereinstimmen.";
    }

    if (!$msg) {
        // Neues Passwort hashen mit Argon2id
        $new_hash = password_hash($new_pw_plain, PASSWORD_ARGON2ID);

        $stmt = $mysqli->prepare("UPDATE tabelle_users SET password = ?, must_change_pw = 0 WHERE id = ?");
        $stmt->bind_param("si", $new_hash, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();

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
                                   placeholder="Neues Passwort" required autocomplete="off"/>
                            <div class="mt-3">
                                <small>Das Passwort muss mindestens 8 Zeichen lang sein, Groß- und Kleinbuchstaben,
                                    eine Zahl und ein Sonderzeichen enthalten.</small>
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
</body>
</html>
