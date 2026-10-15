<?php

use JetBrains\PhpStorm\NoReturn;

session_start();
require '_utils/_csrf.php';


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


function checkUserInSystem(string $username, $mysqli): ?array
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


function assertNotLocked(string $username, mysqli $mysqli): void
{
    $stmt = $mysqli->prepare(
        "SELECT attempts, UNIX_TIMESTAMP(last_attempt) AS last
         FROM tabelle_users WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) return; // unknown user — falls through to the generic error later

    $attempts = (int)$row['attempts'];
    $last = (int)($row['last'] ?? 0);
    $freeTries = 5;

    if ($attempts >= $freeTries && $last > 0) {
        $wait = (int)min(pow(2, $attempts - $freeTries), 3600);
        if (time() - $last < $wait) {
            safeRedirect('index.php?error=login_failed');
        }
    }
}

function recordFailure(string $username, mysqli $mysqli): void
{
    // reset the counter if the last try was >15 min ago, otherwise increment
    $stmt = $mysqli->prepare(
        "UPDATE tabelle_users
         SET attempts = IF(last_attempt < (NOW() - INTERVAL 15 MINUTE) OR last_attempt IS NULL, 1, attempts + 1),
             last_attempt = NOW()
         WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();
}

function recordSuccess(string $username, mysqli $mysqli): void
{
    $stmt = $mysqli->prepare(
        "UPDATE tabelle_users SET attempts = 0, last_attempt = NULL WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();
}


//   ==== MAIN LOGIC  ====

$username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
$password = $_POST["password"] ?? '';
$csrf = $_POST['csrf'] ?? '';

if (!csrf_check($csrf)) {
    safeRedirect('index.php?error=login_failed');
}


if (empty($username) || empty($password)) {
    safeRedirect('index.php?error=login_failed');
}

try {
    $mysqli = getPrivilegedDbConnection();
    if (!$mysqli) {
        safeRedirect('index.php?error=login_failed');
    }
    assertNotLocked($username, $mysqli);
    $userInNewSystem = checkUserInSystem($username, $mysqli);


    if ($userInNewSystem !== null && password_verify($password, $userInNewSystem['password'])) {
        session_regenerate_id(true);
        $_SESSION["username"] = $username;
        $_SESSION["password"] = $userInNewSystem['password'];
        recordSuccess($username, $mysqli);
        $mysqli->close();
        safeRedirect('roombook_projects.php');
    }

    recordFailure($username, $mysqli);

    $mysqli->close();
    safeRedirect('index.php?error=login_failed');

} catch (Exception $e) {
    safeRedirect('index.php?error=login_failed');
}


