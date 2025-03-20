<?php
session_start();

// Function for safe redirection
function safeRedirect($url)
{
    header("Location: $url");
    exit();
}

// Function for logging errors
function logError($message)
{
    error_log($message);
}

// Function to fetch user permissions
function fetch_permissions($mysqli, $username)
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

// Function to check rate limiting
function checkRateLimit($username, $ip) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = array();
    }
    if (!isset($_SESSION['last_attempt'])) {
        $_SESSION['last_attempt'] = array();
    }

    $attempts = isset($_SESSION['login_attempts'][$username]) ? $_SESSION['login_attempts'][$username] : 0;
    $lastAttempt = isset($_SESSION['last_attempt'][$username]) ? $_SESSION['last_attempt'][$username] : 0;
    $currentTime = time();

    // Reset attempts if last attempt was more than 15 minutes ago
    if ($currentTime - $lastAttempt > 900) {
        $attempts = 0;
    }

    if ($attempts >= 5) {
        // Too many attempts, implement delay
        $waitTime = min(pow(2, $attempts - 5), 3600); // Exponential backoff, max 1 hour
        if ($currentTime - $lastAttempt < $waitTime) {
            safeRedirect('index.php?error=too_many_attempts&wait=' . ($waitTime - ($currentTime - $lastAttempt)));
        }
    }
    $_SESSION['login_attempts'][$username] = $attempts + 1;
    $_SESSION['last_attempt'][$username] = $currentTime;
}

$username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
$password = isset($_POST["password"]) ? $_POST["password"] : '';

if (empty($username) || empty($password)) {
    safeRedirect('index.php?error=empty_fields');
}

// Check rate limit before processing login
checkRateLimit($username, $_SERVER['REMOTE_ADDR']);

$hashedPassword = md5($password); // Consider using password_hash() and password_verify() for better security
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

    fetch_permissions($mysqli, $username);
    safeRedirect('projects.php');

} catch (Exception $e) {
    logError($e->getMessage());
    safeRedirect('index.php?error=login_failed');
} finally {
    if (isset($mysqli)) {
        $mysqli->close();
    }
}
