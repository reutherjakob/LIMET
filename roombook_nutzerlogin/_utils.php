<?php
function start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_secure' => true,
            'cookie_samesite' => 'Strict',
            'use_strict_mode' => true,
            'use_only_cookies' => true,
        ]);
    }
}

function send_security_headers(): void
{
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
}

function init_page($allowed_roles): string
{
    start_session();
    check_login_new();
    return check_role_based_access($allowed_roles);
    // close db - Nutzerlogin USer out
    // enable DB connection with group user Acc


}

function check_login_new(): void
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../roombook_nutzerlogin/index.php");
        exit;
    }

}


function check_role_based_access($allowed_roles): string
{
    global $mysqli;
    if (!function_exists('loadEnv')) {
        include "db.php";
    }

    $role = get_user_role();


    if (is_array($allowed_roles)) {
        if (!in_array($role, $allowed_roles, true)) {
            exit;
        }
    } else {
        if ($role !== $allowed_roles) {
            header("Location: ../../roombook_nutzerlogin/index.php");
            exit;
        }
    }
    return $role;
}

function get_user_role(): string
{
    global $mysqli;
    if (!function_exists('loadEnv')) {
        include "db.php";
    }

    $role = "";
    if (isset($_SESSION['user_id'])) {
        $stmt = $mysqli->prepare("SELECT role FROM tabelle_users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($role);
        $stmt->fetch();
        $stmt->close();
    }

    return $role;
}
