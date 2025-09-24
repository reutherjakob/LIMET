<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

function is_strong_password($pw, &$reason = null) {
    $policy = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()]).{12,}$/', $pw);

    $top_passwords = file(__DIR__ . '/top_passwords.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $pw_lower = strtolower($pw);
    $not_blacklisted = !in_array($pw_lower, array_map('strtolower', $top_passwords));

    if (!$policy) {
        $reason = "Password must be at least 12 characters, and include uppercase, lowercase, a number, and a special character.";
        return false;
    }
    if (!$not_blacklisted) {
        $reason = "This password is easily guessable and therefore not allowed. Please change it anywhere else you use this... ";
        return false;
    }
    return true;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pw = $_POST['new_pw'] ?? '';
    if (!is_strong_password($new_pw, $reason)) {
        $msg = $reason;
    } else {
        $hash = password_hash($new_pw, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("UPDATE users SET password = ?, must_change_pw = 0 WHERE id = ?");
        $stmt->bind_param("si", $hash, $_SESSION['user_id']);
        $stmt->execute();
        header("Location: dashboard.php");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <title>Change Password</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<body class="container">
<h2>Change Your Password</h2>
<?php if ($msg) echo "<div class='alert alert-danger'>$msg</div>"; ?>
<form method="POST">
    <input class="form-control mb-3" name="new_pw" type="password" placeholder="New Password" required autocomplete="off">
    <button class="btn btn-primary">Change Password</button>
</form>
<div class="mt-3">
    <small>Password must be at least 12 characters, include upper/lowercase letters, a number, and a special character.</small>
</div>
</body>
</html>