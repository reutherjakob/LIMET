<?php
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

define('MAX_ATTEMPTS', 5);
define('LOCKOUT_TIME', 15 * 60); // 15 minutes
define('RATE_LIMIT', 5); // max 10 login attempts per IP per 10 minutes
define('RATE_LIMIT_WINDOW', 600);

function clean($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

// Check rate limit by IP
function rate_limit_check($mysqli, $ip) {
    $window_start = date('Y-m-d H:i:s', time() - RATE_LIMIT_WINDOW);
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND attempt_time > ?");
    $stmt->bind_param("ss", $ip, $window_start);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count < RATE_LIMIT;
}

// Log login attempt (for audit and rate limiting)
function log_attempt($mysqli, $ip, $success) {
    $stmt = $mysqli->prepare("INSERT INTO login_attempts (ip, attempt_time, success) VALUES (?, NOW(), ?)");
    $stmt->bind_param("si", $ip, $success);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_SERVER['REMOTE_ADDR'];
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf'] ?? '';

    if (!csrf_check($csrf)) {
        echo "Invalid request.";
        exit;
    }

    // Rate limit check
    if (!rate_limit_check($mysqli, $ip)) {
        echo "Too many attempts from your IP. Try again later.";
        exit;
    }

    if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username) || strlen($password) < 12) {
        log_attempt($mysqli, $ip, 0);
        echo "Invalid credentials.";
        exit;
    }

    $stmt = $mysqli->prepare("SELECT id, password, attempts, last_attempt, must_change_pw FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows) {
        $stmt->bind_result($id, $hash, $attempts, $last_attempt, $must_change_pw);
        $stmt->fetch();

        // Check lockout
        if ($attempts >= MAX_ATTEMPTS && (time() - strtotime($last_attempt)) < LOCKOUT_TIME) {
            log_attempt($mysqli, $ip, 0);
            echo "Account locked. Please try again later.";
            exit;
        }

        if ($password=== $hash) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            // Reset attempts after successful login
            $reset = $mysqli->prepare("UPDATE users SET attempts = 0 WHERE id = ?");
            $reset->bind_param("i", $id);
            $reset->execute();
            log_attempt($mysqli, $ip, 1);
            if ($must_change_pw) {
                echo "change_pw"; // JS will redirect!
                exit;
            } else {
                echo "success";
                exit;
            }
        } else {
            $attempts++;
            $update = $mysqli->prepare("UPDATE users SET attempts = ?, last_attempt = NOW() WHERE id = ?");
            $update->bind_param("ii", $attempts, $id);
            $update->execute();
            log_attempt($mysqli, $ip, 0);
            if ($attempts >= MAX_ATTEMPTS) {
                echo "Account locked due to too many failed attempts.";
            } else {
                echo "Invalid credentials.";
            }
        }
    } else {
        log_attempt($mysqli, $ip, 0);
        echo "Invalid credentials.";
    }
    exit;
}
?>