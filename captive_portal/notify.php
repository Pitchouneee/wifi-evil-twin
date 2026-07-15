<?php
if (!defined('DASHBOARD_URL')) {
    define('DASHBOARD_URL', getenv('DASHBOARD_URL') ?: 'http://192.168.1.50:8080/context');
}

/**
 * Increments a counter on the dashboard, fire-and-forget (non-blocking)
 * Runs curl in the background: impacts neither the portal rendering nor the login
 *
 * @param string $type 'attempt' or 'connection'
 */
function notify_dashboard($type)
{
    if (!in_array($type, ['attempt', 'connection'], true)) {
        return;
    }

    $url = rtrim(DASHBOARD_URL, '/') . '/increment.php?type=' . rawurlencode($type);

    // -s: silent, -m 3: 3s timeout, &: background (does not block PHP)
    exec('curl -s -m 3 ' . escapeshellarg($url) . ' > /dev/null 2>&1 &');
}
