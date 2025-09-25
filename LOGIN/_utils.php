<?php

function init_page($allowed_roles)
{
    session_start();
    check_login_new();
    include "db.php";
    check_role_based_access($allowed_roles);

}

function check_login_new()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.html");
        exit;
    }

}

function check_role_based_access($allowed_roles)
{
    $role = "";
    $stmt = $mysqli->prepare("SELECT role FROM users WHERE id = ?");
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
            exit;
        }
    }
}
