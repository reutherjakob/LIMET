<?php
use JetBrains\PhpStorm\NoReturn;

session_start();
require 'utils/csrf.php';


#[NoReturn] function safeRedirect($url): void
  {
    header("Location: $url");
    exit();
}

// Function for logging errors
function logError($message): void
{
    error_log($message);
}


/**
 * @throws Exception
 */
function fetch_permissions($mysqli, $username): int
{
    $stmt = $mysqli->prepare("SELECT permission FROM tabelle_user_permission WHERE user = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row ? (int)$row["permission"] : 0;
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
            safeRedirect('index.php?error=too_many_attempts'.$_SERVER['REMOTE_ADDR'].'&wait=' . ($waitTime - ($currentTime - $lastAttempt)));
        }
    }
    $_SESSION['login_attempts_ip'][$ip] = $attempts + 1;
    $_SESSION['last_attempt_ip'][$ip] = $currentTime;
}




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
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed");
    }

    // Successful login, reset attempts
    unset($_SESSION['login_attempts'][$username]);
    unset($_SESSION['last_attempt'][$username]);

    $_SESSION["username"] = $username;
    $_SESSION["password"] = $hashedPassword;

    //fetch_permissions($mysqli, $username);
    safeRedirect('projects.php');

} catch (Exception $e) {
    logError($e->getMessage());
    safeRedirect('index.php?error=login_failed');
} finally {
    if (isset($mysqli)) {

        $mysqli->close();

    }

}
