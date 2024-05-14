<?php

function br2nl($string) {
    $return = str_replace(array("<br/>"), "\n", $string);
    //  $return= str_replace(array("\r\n", "\n\r", "\r", "\n"), "<br/>", $temp);
    return $return;
}


function check_if_project_selected_else_redirect() {
    if ($_SESSION["projectName"] == "") {
        header("Location: https://work.limet-rb.com/projects.php");
        exit;
    }
}

function check_login() {
    if (!isset($_SESSION["username"])) {
        echo "Bitte erst <a href=\"index.php\">einloggen</a>";
        exit;
    }
}

function get_project() {
    if ($_SESSION["projectName"] != "") {
        echo '<script>';
        echo 'var currentP = ' . json_encode($_SESSION["projectName"]) . ';';
        echo '</script>';
    } else {
        echo '<script>';
        echo 'var currentP = ' . json_encode(" NIX ") . ';';
        echo '</script>';
    }
}

function init_page_serversides($ommit_redirect = "") { //and Project. 
    check_login();
    get_project();
    load_nav_bar();
    if ($ommit_redirect == "") {
        check_if_project_selected_else_redirect();
    }


function utils_connect_sql() {
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

function load_nav_bar() {
    echo '<script>';
    echo '    window.onload = function () {';
    echo '        $.get("navbar.html", function (data) {';
    echo '            $("#limet-navbar").html(data);';
    echo '            $(".navbar-nav").find("li:nth-child(3)")';
    echo '                    .addClass("active");';
    echo '            $("#projectSelected").text("Projekt:" + currentP);';
    echo '        });';
    echo '     };    </script>';

}


?>
