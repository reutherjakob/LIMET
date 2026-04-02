<?php
global $mysqli;
require 'db.php';

require_once "../Nutzerlogin/_utils.php";
start_session();
require 'csrf.php';

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; script-src 'self' https://code.jquery.com https://cdn.jsdelivr.net");
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: geolocation=(), camera=(), microphone=()');
const MAX_ATTEMPTS = 10;
const LOCKOUT_TIME = 20 * 60;
const RATE_LIMIT = 10;
const RATE_LIMIT_WINDOW = 300;


function clean($str): string
{
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function rate_limit_check($mysqli, $ip, $username): bool
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
    $password = $_POST['password'] ?? ''; //$_POST['hashed_password'] ?? '';
    //$password = password_hash($password, PASSWORD_ARGON2ID);
    $csrf = $_POST['csrf'] ?? '';

    if (!csrf_check($csrf)) {
        log_attempt($mysqli, $ip, $username, 0);
        echo json_encode(['status' => 'error', 'msg' => 'Ungültige Zugangsdaten.', 'csrf' => csrf_token()]);
        exit;
    }

    if (!rate_limit_check($mysqli, $ip, $username)) {
        log_attempt($mysqli, $ip, $username, 0);
        echo json_encode(['status' => 'error', 'msg' => 'Zu viele Versuche. Bitte probieren sie es später erneut.', 'csrf' => csrf_token()]);
        exit;
    }

    if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
        log_attempt($mysqli, $ip, $username, 0);
        echo json_encode(['status' => 'error', 'msg' => 'Ungültige Zugangsdaten.', 'csrf' => csrf_token()]);
        exit;
    }

    $stmt = $mysqli->prepare("SELECT id, password, attempts, last_attempt, must_change_pw FROM tabelle_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();


    if ($stmt->num_rows) {
        $stmt->bind_result($id, $hash, $attempts, $last_attempt, $must_change_pw); #hash aus der sb
        $stmt->fetch();

        if ($attempts >= MAX_ATTEMPTS && (time() - strtotime($last_attempt)) < LOCKOUT_TIME) { // Works
            log_attempt($mysqli, $ip, $username, 0);
            echo json_encode(['status' => 'error', 'msg' => 'Zu viele Versuche. Probieren Sie es später erneut.', 'csrf' => csrf_token()]);
            exit;
        }

        if (password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $username;
            $reset = $mysqli->prepare("UPDATE tabelle_users SET attempts = 0 WHERE id = ?");
            $reset->bind_param("i", $id);
            $reset->execute();
            log_attempt($mysqli, $ip, $username, 1);
            if ($must_change_pw) {
                echo json_encode(['status' => 'change_pw']);
            } else {
                echo json_encode(['status' => 'success']);
            }
        } else {
            $attempts++;
            $update = $mysqli->prepare("UPDATE tabelle_users SET attempts = ?, last_attempt = NOW() WHERE id = ?");
            $update->bind_param("ii", $attempts, $id);
            $update->execute();
            log_attempt($mysqli, $ip, $username, 0);
            echo json_encode(['status' => 'error', 'msg' => 'Ungültige Zugangsdaten.', 'csrf' => csrf_token()]);
        }
        $mysqli->close();
    } else {
        password_verify($password, '$argon2id$v=19$m=65536,t=4,p=1$Fe5EPJVPe71vIyIAcIDzyA$8Vama06zrsly0E+mM6rZKyr2RQE9V+x/VtMcwOw7Al0');
        log_attempt($mysqli, $ip, $username, 0);
        echo json_encode(['status' => 'error', 'msg' => 'Ungültige Zugangsdaten.', 'csrf' => csrf_token()]);
        $mysqli->close();
    }
    exit;
}
