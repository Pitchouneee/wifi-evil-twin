<?php
/**
 * Counter read endpoint (used by the dashboard for auto-refresh)
 * Response: JSON { "attempt": N, "connection": M }
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/lib.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    echo json_encode(stats_read());
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'database error']);
}
