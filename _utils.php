<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function echorow($row)
{
    echo '<pre>';
    print_r($row);
    echo '</pre>';
}

function init_page_serversides($ommit_redirect = "", $noscroll = "")
{
    check_login();
    get_project();
    if ($ommit_redirect == "") {
        check_if_project_selected_else_redirect();
    }
    load_nav_bar();
    if ($noscroll == "") {
        include '_scrollUpBtn.php';
    }
}

function check_login()
{
    if (!isset($_SESSION["username"])) {
        echo "Bitte erst <a href=\"index.php\">einloggen</a>";
        exit;
    }
}

function check_if_project_selected_else_redirect()
{
    if ($_SESSION["projectName"] == "") {
        header("Location: /projects.php");
        exit;
    }
}

function get_project()
{
    echo '<script>';
    if (isset($_SESSION["projectName"])) {
        echo 'var currentP = ' . json_encode($_SESSION["projectName"]) . ';';
    } else {
        echo 'var currentP = ' . json_encode(" KEIN PROJEKT AUSGEWÄHLT ") . ';';
    }
    echo '</script>';
}

function br2nl($string): array|string
{
    if ($string != null) {
        $string = str_replace(array("<br/>"), "\n", $string);
        $string = str_replace(array("</br>"), "\n", $string);
        return str_replace(array("<br>"), "\n", $string);
    } else {
        return "";
    }
}

function utils_connect_sql()
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $mysqli = new mysqli('localhost', $_SESSION["username"], $_SESSION["password"], 'LIMET_RB');
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;

    }
    if (!$mysqli->set_charset("utf8")) {
        printf("Error loading character set utf8: %s\n", $mysqli->error);
        exit();
    }
    return $mysqli;
}

function load_nav_bar()
{
    echo '<script>';
    echo '    window.onload = function () {';
    echo '        jQuery.get("navbar.html", function (data) {';
    echo '            jQuery("#limet-navbar").html(data);';
    echo '           jQuery(".navbar-nav").find("li:nth-child(3)")';
    echo '                    .addClass("active");';
    echo '           jQuery("#projectSelected").text(currentP);';
    echo '        });';
    echo '     };    </script>';
}


