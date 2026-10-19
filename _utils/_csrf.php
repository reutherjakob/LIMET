<?php

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check($token)
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function require_csrf(): void
{
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf'] ?? '';
    if (!csrf_check($token)) {
        http_response_code(403);
        echo json_encode(['error' => true, 'message' => 'Ungültiges Token']);
        exit;
    }
}
?>