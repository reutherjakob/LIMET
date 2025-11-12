<?php

if (__FILE__ === $_SERVER['SCRIPT_FILENAME']) {    // Datei wurde direkt aufgerufen
    http_response_code(404);
    exit;
}
if (!function_exists('loadEnv')) {
    function loadEnv($file = '/var/www/vhosts/limet-rb.com/CONFIG/.env'): void
    {
        if (!file_exists($file)) return;
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            [$name, $value] = array_map('trim', explode('=', $line, 2));
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv("$name=$value");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}


loadEnv();

$mysqli = new mysqli(
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_NAME')
);

if ($mysqli->connect_errno) {
    error_log("DB Error: " . $mysqli->connect_error);
    die("Database error.");
}