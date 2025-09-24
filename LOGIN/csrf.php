<?php
// CSRF token generator and checker
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function csrf_check($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
?>