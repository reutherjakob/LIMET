<?php

if (session_status() == PHP_SESSION_NONE) {
    // Only send the session cookie over HTTPS connections
    ini_set('session.cookie_secure', '1');
    // Make session cookies inaccessible to JavaScript (prevents XSS stealing)
    ini_set('session.cookie_httponly', '1');
    // Restrict cookie to your site's path to prevent cross-site cookie usage (optional)
    ini_set('session.cookie_path', '/');
    // Additionally, consider SameSite attribute for cookies (PHP 7.3+)
    session_set_cookie_params([
        'lifetime' => 0,         // Session cookie lasts until browser closes
        'path' => '/',
        'domain' => '',          // Set domain if needed
        'secure' => true,        // Cookie sent only over HTTPS
        'httponly' => true,      // JavaScript can't access cookie
        'samesite' => 'Strict'   // Or 'Lax' depending on your needs
    ]);
    session_start();
}

function init_page_serversides($ommit_redirect = "", $noscroll = ""): void
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

function check_login(): void
{
    if (!isset($_SESSION["username"])) {
        echo '<div class="container-fluid bg-white py-5"> 
            <div class="row justify-content-center">
                <div class="col-12 col-md-6">
                    <div class="card shadow">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-3">Nicht eingeloggt</h5>
                            <p class="card-text">
                                Bitte erst <a href="/index.php">einloggen</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        exit;
    }
}

function check_if_project_selected_else_redirect(): void
{
    if (!isset($_SESSION['projectID']) || !is_numeric($_SESSION['projectID'])) {
        header("Location: /projects.php");
        exit;
    }

}

function get_project(): void
{
    echo '<script>';
    if (isset($_SESSION["projectName"])) {
        echo 'var currentP = ' . json_encode($_SESSION["projectName"]) . ';';
    } else {
        echo 'var currentP = ' . json_encode(" Kein Projekt ") . ';';
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

function load_nav_bar(): void
{
    echo '<script>';
    echo '    window.onload = function () {';
    echo '        jQuery.get("/utils/navbar.html", function (data) {';
    echo '            jQuery("#limet-navbar").html(data);';
    echo '           jQuery(".navbar-nav").find("li:nth-child(3)")';
    echo '                    .addClass("active");';
    echo '           jQuery("#projectSelected").text(currentP);';
    echo '        });';
    echo '     };    </script>';
}


function getPostInt(string $key, int $default = 0): int
{
    return isset($_POST[$key]) ? filter_var($_POST[$key], FILTER_VALIDATE_INT) ?? $default : $default;
}

function getPostString(string $key, string $default = ''): string
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

function getPostDate(string $key): string
{
    $dateInput = getPostString($key); // e.g. '2025-11-07'
    $dateFormatted = '';

    if ($dateInput !== '') {
        $timestamp = strtotime($dateInput);
        if ($timestamp !== false) {
            $dateFormatted = date("Y-m-d", $timestamp); // expected to be identical here
        }
    }
    return $dateFormatted;
}

function getPostFloat(string $key, float $default = 0.0): float {
    return isset($_POST[$key]) ? filter_var($_POST[$key], FILTER_VALIDATE_FLOAT) ?? $default : $default;
}

function getPostArrayInt(string $key, array $default = []): array {
    $input = filter_input(INPUT_POST, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!$input) {
        return $default;
    }
    $output = [];
    foreach ($input as $item) {
        $val = filter_var($item, FILTER_VALIDATE_INT);
        if ($val !== false) {
            $output[] = $val;
        }
    }
    return $output;
}
