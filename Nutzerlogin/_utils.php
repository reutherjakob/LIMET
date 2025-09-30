<?php


function init_page($allowed_roles): void
{
    session_start();
    check_login_new();
    check_role_based_access($allowed_roles);
    // close db - Nutzerlogin USer out
    // enable DB connection with group user Acc

}

function check_login_new(): void
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../Nutzerlogin/index.php");
        exit;
    }

}

function check_role_based_access($allowed_roles): void
{
    global $mysqli;
    if (!function_exists('loadEnv')) {
        include "db.php";
    }

    $role = "";
    $stmt = $mysqli->prepare("SELECT role FROM tabelle_users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();


    if (is_array($allowed_roles)) {
        if (!in_array($role, $allowed_roles, true)) {
            exit;
        }
    } else {
        if ($role !== $allowed_roles) {
            echo "HOW DID U GET HERE?";
            // TODO LOG ATTEMPT?
            exit;

        }
    }
}

function get_user_role() {
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
