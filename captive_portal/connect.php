<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : 'Unknown';
    // $password = isset($_POST['password']) ? $_POST['password'] : '';

    $user_ip = $_SERVER['REMOTE_ADDR'];

    $date = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];

    $logEntry = sprintf("[%s] IP: %s | AD_User: %s\n", $date, $ip, $username);

    // Write to the counter.log file
    // FILE_APPEND avoids overwriting the file each time
    file_put_contents('counter.log', $logEntry, FILE_APPEND | LOCK_EX);

    // Counter: number of connections
    require_once __DIR__ . '/notify.php';
    notify_dashboard('connection');

    // Remove the redirection for this specific IP
    exec("sudo iptables -t nat -I PREROUTING -s " . $user_ip . " -j ACCEPT");

    // echo "<h1>Security awareness</h1>";
    // echo "<p>Thank you! You now have Internet access. However, stay alert: this public Wi-Fi was a security test...</p>";
    echo "<script>setTimeout(function(){ window.location.href = 'https://www.google.com'; }, 1000);</script>";
}
?>