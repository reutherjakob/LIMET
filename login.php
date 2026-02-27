<?php

use JetBrains\PhpStorm\NoReturn;

session_start();
require 'utils/csrf.php';

error_log("test");

//  login_helpers.php
//  Helper-Funktionen für die Migration von MD5 zu Argon2id

function getPrivilegedDbConnection(): ?mysqli
{

    $envFile = '/var/www/vhosts/limet-rb.com/CONFIG/.env';

    if (!file_exists($envFile)) {
        //  error_log("ENV file not found: $envFile");
        return null;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $config = [];

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$name, $value] = array_map('trim', explode('=', $line, 2));
        $config[$name] = $value;
    }

    $mysqli = new mysqli(
        $config['DB_HOST'] ?? 'localhost',
        $config['DB_USER'] ?? '',
        $config['DB_PASS'] ?? '',
        $config['DB_NAME'] ?? 'LIMET_RB'
    );

    if ($mysqli->connect_errno) {
        error_log("Privileged DB connection failed: " . $mysqli->connect_error);
        return null;
    }

    return $mysqli;
}


#[NoReturn] function safeRedirect($url): void
{
    header("Location: $url");
    exit();
}

function migrateUserToNewSystem(string $username, string $plainPassword, $mysqli): bool
{

    if (!$mysqli) return false;
    $check = $mysqli->prepare("SELECT id, password FROM tabelle_users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows == 0) {
        $check->close();
        $argon2Hash = password_hash($plainPassword, PASSWORD_ARGON2ID);
        $insert = $mysqli->prepare("INSERT INTO tabelle_users (username, password, role, must_change_pw, attempts) VALUES (?, ?, 'internal_rb_user', 0, 0)");
        $insert->bind_param("ss", $username, $argon2Hash);
        $success = $insert->execute();
        $insert->close();
        return $success;
    }

    return false;
}


function checkUserInNewSystem(string $username, $mysqli): ?array
{
    if (!$mysqli) return null;

    $stmt = $mysqli->prepare("SELECT id, password, role FROM tabelle_users WHERE username = ? AND role = 'internal_rb_user'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = null;
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    }
    return $data;
}

function checkRateLimit($ip): void
{
    if (!isset($_SESSION['login_attempts_ip'])) {
        $_SESSION['login_attempts_ip'] = array();
    }
    if (!isset($_SESSION['last_attempt_ip'])) {
        $_SESSION['last_attempt_ip'] = array();
    }

    $attempts = $_SESSION['login_attempts_ip'][$ip] ?? 0;
    $lastAttempt = $_SESSION['last_attempt_ip'][$ip] ?? 0;
    $currentTime = time();
    if ($currentTime - $lastAttempt > 900) {
        $attempts = 0;
    }
    if ($attempts >= 3) {        // Too many attempts, implement delay
        $waitTime = min(pow(2, $attempts - 5), 3600); // Exponential backoff, max 1 hour
        if ($currentTime - $lastAttempt < $waitTime) {
            safeRedirect('index.php?error=too_many_attempts' . $_SERVER['REMOTE_ADDR'] . '&wait=' . ($waitTime - ($currentTime - $lastAttempt)));
        }
    }
    $_SESSION['login_attempts_ip'][$ip] = $attempts + 1;
    $_SESSION['last_attempt_ip'][$ip] = $currentTime;
}


//   ==== MAIN LOGIC  ====

$username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
$password = $_POST["password"] ?? '';
$csrf = $_POST['csrf'] ?? '';

if (!csrf_check($csrf)) {
    echo "Ungültige Zugangsdaten.";
    exit;
}


if (empty($username) || empty($password)) {
    safeRedirect('index.php?error=empty_fields');
}

checkRateLimit($_SERVER['REMOTE_ADDR']);
$hashedPassword = md5($password);

try {
    $mysqli = new mysqli('localhost', $username, $hashedPassword, 'LIMET_RB');
    // $mysqli = getPrivilegedDbConnection();   //use if hashed pw are removed
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed");
    }

    $userInNewSystem = checkUserInNewSystem($username, $mysqli);
    if ($userInNewSystem === null) {
        migrateUserToNewSystem($username, $password, $mysqli);
        unset($_SESSION['login_attempts'][$username]);
        unset($_SESSION['last_attempt'][$username]);
        $_SESSION["username"] = $username;
        $_SESSION["password"] = $hashedPassword;
        //fetch_permissions($mysqli, $username);
        safeRedirect('projects.php');

    } else {

        if (!$mysqli) return false;
        $check = $mysqli->prepare("SELECT id, password FROM tabelle_users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        $hashedPassword = md5($password);

        if (!empty($userInNewSystem['password']) && strlen($userInNewSystem['password']) > 32) {
            if (password_verify($password, $userInNewSystem['password'])) {
                session_regenerate_id(true);
                $_SESSION["username"] = $username;
                $_SESSION["password"] = $hashedPassword; // Noch für SQL-Connection nötig
                unset($_SESSION['login_attempts'][$username]);
                unset($_SESSION['last_attempt'][$username]);
                //   error_log("LOGIN SUCCESS: Internal user '$username' via new system (Argon2id)");
                safeRedirect('projects.php');
            } else {
                // error_log("LOGIN FAILED: Invalid password for internal user '$username' (new system)");
                safeRedirect('index.php?error=login_failed');
            }
        }
    }
} catch (Exception $e) {
    // error_log($e->getMessage());
    safeRedirect('index.php?error=login_failed');
} finally {
    if (isset($mysqli)) {
        $mysqli->close();
    }
}


