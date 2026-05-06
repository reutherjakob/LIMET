<?php
// 25 FX
require_once 'utils/_utils.php';
check_login();

$reportUrl  = filter_input(INPUT_POST, 'reportUrl',  FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'unknown';
$reportText = filter_input(INPUT_POST, 'reportText', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
$roomIDs    = filter_input(INPUT_POST, 'roomIDs',    FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

$projectID   = $_SESSION['projectID']   ?? '?';
$projectName = $_SESSION['projectName'] ?? '?';
$username    = $_SESSION['username']    ?? '?';

$timestamp = date('Y-m-d H:i:s');

$line = implode(' | ', [
        $timestamp,
        "User: $username",
        "Projekt: $projectID – $projectName",
        "Bericht: $reportText ($reportUrl)",
        "Räume: $roomIDs"
    ]) . PHP_EOL;

$logFile = __DIR__ . '/logs/report_downloads.log';

// Verzeichnis anlegen falls nicht vorhanden
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0750, true);
}

file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);

http_response_code(204); // No Content