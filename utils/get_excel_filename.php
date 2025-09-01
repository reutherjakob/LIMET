<?php
session_start();
header('Content-Type: application/json');

// Helper
function safe_trim($val, $default = '') {
    return isset($val) && is_string($val) ? trim($val) : ($val ?? $default);
}

// Define your project-specific conventions (without extension)
$namingConventions = [
    'default' => [
        'parts' => ['projectName', 'planungsphase', 'placeholder', 'department', 'documentName', 'date'],
        'separator' => '_'
    ],
    'KHI' => [
        'parts' => ['projectName', 'planungsphase', 'placeholder', 'department', 'documentName', 'date'],
        'separator' => '_'
    ],

];

// Gather session and request data
$projectName = safe_trim($_SESSION['projectName'] ?? 'PROJECT');
$planungsphase = safe_trim($_SESSION['projectPlanungsphase'] ?? '');
$documentName = safe_trim($_POST['documentName'] ?? $_GET['documentName'] ?? '');
$department = safe_trim($_POST['department'] ?? $_GET['department'] ?? 'GPMT');
$date = safe_trim($_POST['date'] ?? $_GET['date'] ?? date('Y-m-d'));
$placeholder = safe_trim($_POST['placeholder'] ?? $_GET['placeholder'] ?? '');

// Determine convention
$projectKey = $projectName ?: 'default';
$convention = $namingConventions[$projectKey] ?? $namingConventions['default'];
$separator = $convention['separator'] ?? '_';

// Build part values
$partValues = [
    'projectName' => $projectName,
    'planungsphase' => $planungsphase,
    'department' => $department,
    'documentName' => $documentName,
    'date' => $date,
    'placeholder' => $placeholder
];

// Assemble filename
$parts = array_map(function($key) use ($partValues) {
    return safe_trim($partValues[$key] ?? '');
}, $convention['parts']);
$filename = preg_replace('/[\\/:*?"<>|]+/', '', implode($separator, $parts)) . '.xlsx';

// Return as JSON
echo json_encode(['filename' => $filename]);
