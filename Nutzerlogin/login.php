<?php
global $mysqli;
require 'db.php';

session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Strict'
]);
require 'csrf.php';

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; script-src 'self' https://code.jquery.com https://cdn.jsdelivr.net");

const MAX_ATTEMPTS = 5;
const LOCKOUT_TIME = 20 * 60;
const RATE_LIMIT = 5;
const RATE_LIMIT_WINDOW = 300;


function clean($str): string
{
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function rate_limit_check($mysqli, $ip, $username)
{
    $window_start = date('Y-m-d H:i:s', time() - RATE_LIMIT_WINDOW);

    $stmt_ip = $mysqli->prepare("SELECT COUNT(*) FROM tabelle_login_attempts WHERE ip = ? AND attempt_time > ?");
    $stmt_ip->bind_param("ss", $ip, $window_start);
    $stmt_ip->execute();
    $stmt_ip->bind_result($count_ip);
    $stmt_ip->fetch();
    $stmt_ip->close();

    if ($count_ip >= RATE_LIMIT) {
        return false;
    }
+
    $stmt_user = $mysqli->prepare("SELECT COUNT(*) FROM tabelle_login_attempts WHERE username = ? AND attempt_time > ?");
    $stmt_user->bind_param("ss", $username, $window_start);
    $stmt_user->execute();
    $stmt_user->bind_result($count_user);
    $stmt_user->fetch();
    $stmt_user->close();

    if ($count_user >= RATE_LIMIT) {
        return false;
    }

    return true;
}


function log_attempt($mysqli, $ip, $username, $success): void
{
    $stmt = $mysqli->prepare("INSERT INTO tabelle_login_attempts (ip, username, attempt_time, success) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("ssi", $ip, $username, $success);
    $stmt->execute();
    $stmt->close();

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_SERVER['REMOTE_ADDR'];
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['hashed_password'] ?? '';
    $csrf = $_POST['csrf'] ?? '';

    if (!csrf_check($csrf)) {
        log_attempt($mysqli, $ip, $username, 0);
        echo "Ungültige Zugangsdaten.";
        exit;
    }

    if (!rate_limit_check($mysqli, $ip, $username)) {
        log_attempt($mysqli, $ip, $username, 0);
        echo "Ungültige Zugangsdaten.";
        exit;
    }

    if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
        log_attempt($mysqli, $ip, $username, 0);
        echo "Ungültige Zugangsdaten.";
        exit;
    }

    $stmt = $mysqli->prepare("SELECT id, password, attempts, last_attempt, must_change_pw FROM tabelle_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();


    if ($stmt->num_rows) {
        $stmt->bind_result($id, $hash, $attempts, $last_attempt, $must_change_pw);
        $stmt->fetch();

        if ($attempts >= MAX_ATTEMPTS && (time() - strtotime($last_attempt)) < LOCKOUT_TIME) { // Works
            log_attempt($mysqli, $ip, $username, 0);
            echo "Zu viele Versuche. Probieren Sie es später erneut. ";
            exit;
        }

        if ($password === $hash) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $reset = $mysqli->prepare("UPDATE tabelle_users SET attempts = 0 WHERE id = ?");
            $reset->bind_param("i", $id);
            $reset->execute();
            log_attempt($mysqli, $ip, $username, 1);
            if ($must_change_pw) {
                echo "change_pw";
            } else {
                echo "success";
            }
        } else {
            $attempts++;
            $update = $mysqli->prepare("UPDATE tabelle_users SET attempts = ?, last_attempt = NOW() WHERE id = ?");
            $update->bind_param("ii", $attempts, $id);
            $update->execute();
            log_attempt($mysqli, $ip, $username, 0);
            echo "Ungültige Zugangsdaten.";
        }
        $mysqli->close();
    } else {
        log_attempt($mysqli, $ip, $username, 0);
        echo "Ungültige Zugangsdaten.";
    }
    exit;
}
