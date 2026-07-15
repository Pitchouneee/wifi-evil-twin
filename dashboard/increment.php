<?php
/**
 * Counter increment endpoint
 *
 * The two requested "endpoints" map to the `type` parameter:
 *   - increment.php?type=attempt     -> portal access attempt
 *   - increment.php?type=connection  -> connection (form submitted)
 *
 * Response: JSON { "ok": true, "stats": { "attempt": N, "connection": M } }
 */

require_once __DIR__ . '/lib.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$type = $_GET['type'] ?? $_POST['type'] ?? '';

try {
    if (!stats_increment($type)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'invalid type (attempt|connection)']);
        exit;
    }

    echo json_encode(['ok' => true, 'stats' => stats_read()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'database error']);
}
