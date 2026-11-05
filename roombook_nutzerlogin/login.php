<?php
global $mysqli;
require 'db.php';

require_once "../roombook_nutzerlogin/_utils.php";
start_session();
require 'csrf.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; script-src 'self' https://code.jquery.com https://cdn.jsdelivr.net");
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: geolocation=(), camera=(), microphone=()');
const MAX_ATTEMPTS = 10;
const LOCKOUT_TIME = 20 * 60;
const RATE_LIMIT = 20;
const RATE_LIMIT_WINDOW = 300;


function count_failed_attempts(mysqli $mysqli, string $column, string $value, int $window): int
{
    $sql = "SELECT COUNT(*) FROM tabelle_login_attempts
            WHERE $column = ? AND success = 0 AND attempt_time > NOW() - INTERVAL ? SECOND";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("si", $value, $window);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return (int)$count;
}

function rate_limit_check(mysqli $mysqli, string $ip, string $username): bool
{
    return count_failed_attempts($mysqli, 'ip', $ip, RATE_LIMIT_WINDOW) < RATE_LIMIT
        && count_failed_attempts($mysqli, 'username', $username, LOCKOUT_TIME) < MAX_ATTEMPTS;
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
    $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $csrf = $_POST['csrf'] ?? '';

    if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
        $username = '';
    }

    if (!csrf_check($csrf)) {
        log_attempt($mysqli, $ip, $username, 0);
        echo json_encode(['status' => 'error', 'msg' => 'Ungültige Zugangsdaten.', 'csrf' => csrf_token()]);
        exit;
    }

    if (!rate_limit_check($mysqli, $ip, $username)) {
        echo json_encode(['status' => 'error', 'msg' => 'Zu viele Versuche. Bitte probieren Sie es später erneut.', 'csrf' => csrf_token()]);
        exit;
    }

    if ($username === '') {
        log_attempt($mysqli, $ip, $username, 0);
        echo json_encode(['status' => 'error', 'msg' => 'Ungültige Zugangsdaten.', 'csrf' => csrf_token()]);
        exit;
    }

    $stmt = $mysqli->prepare("SELECT id, password, must_change_pw FROM tabelle_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();


    if ($stmt->num_rows) {
        $stmt->bind_result($id, $hash, $must_change_pw); #hash aus der sb
        $stmt->fetch();

        if (password_verify($password, $hash)) {
            session_regenerate_id(true);
            unset($_SESSION['csrf_token'], $_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['pw_change_user_id']);
            log_attempt($mysqli, $ip, $username, 1);
            if ($must_change_pw) {
                // Noch kein Login: init_page() lässt erst nach dem Passwortwechsel durch.
                $_SESSION['pw_change_user_id'] = $id;
                echo json_encode(['status' => 'change_pw']);
            } else {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $username;
                echo json_encode(['status' => 'success']);
            }
        } else {
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
