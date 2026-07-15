<?php
// HTTP Basic authentication for the dashboard
$expectedUser = getenv('DASHBOARD_USER') ?: 'admin';
$expectedPass = getenv('DASHBOARD_PASSWORD') ?: '';

$user = $_SERVER['PHP_AUTH_USER'] ?? '';
$pass = $_SERVER['PHP_AUTH_PW'] ?? '';

// Fail-closed: if no password is configured, deny everything
// hash_equals: constant-time comparison (anti timing-attack)
$ok = $expectedPass !== ''
    && hash_equals($expectedUser, $user)
    && hash_equals($expectedPass, $pass);

if (!$ok) {
    header('WWW-Authenticate: Basic realm="evil_twin"');
    header('HTTP/1.1 401 Unauthorized');
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Authentication required.';
    exit;
}
