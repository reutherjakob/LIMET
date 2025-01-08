<?php
function safeRedirect($url)
{
    header("Location: $url");
    exit();
}

function logError($message)
{
    error_log($message);
}

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

session_start();
$username = isset($_POST["username"]) ? trim($_POST["username"]) : '';
$password = isset($_POST["password"]) ? $_POST["password"] : '';


if (empty($username) || empty($password)) {
    safeRedirect('index.php?error=empty_fields');
}


$hashedPassword = md5($password);
try {
    $mysqli = new mysqli('localhost', $username, $hashedPassword, 'LIMET_RB');
    if ($mysqli->connect_error) {
        safeRedirect('index.php');
    }

    $_SESSION["username"] = $username;
    $_SESSION["password"] = $hashedPassword;

    fetch_permissions($mysqli, $username);
    safeRedirect('projects.php');

} catch (Exception $e) {
    logError($e->getMessage());
    // safeRedirect('index.php?error=login_failed');  // Finally works, but requires anti bruteforce measures
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($mysqli)) {
        $mysqli->close();
    }
}
