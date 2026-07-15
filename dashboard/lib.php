<?php
// Allowed counter types (= keys of the `counters` table)
function stats_allowed_types()
{
    return ['attempt', 'connection'];
}

/**
 * Returns a PDO connection
 */
function db_connect()
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $cfg = require __DIR__ . '/config.php';
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $cfg['host'],
        $cfg['port'],
        $cfg['dbname'],
        $cfg['charset']
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Small retry: on docker-compose startup, the app may be ready
    // before MariaDB. We try a few times before giving up
    $attempts = (int) (getenv('DB_CONNECT_RETRIES') ?: 10);
    for ($i = 1; ; $i++) {
        try {
            $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
            return $pdo;
        } catch (PDOException $e) {
            if ($i >= $attempts) {
                throw $e;
            }
            sleep(1);
        }
    }
}

/**
 * Increments a counter atomically
 * @param string $type 'attempt' or 'connection'
 * @return bool true if incremented, false if invalid type
 */
function stats_increment($type)
{
    if (!in_array($type, stats_allowed_types(), true)) {
        return false;
    }

    $pdo = db_connect();
    $stmt = $pdo->prepare(
        'INSERT INTO counters (name, value) VALUES (:name, 1)
         ON DUPLICATE KEY UPDATE value = value + 1'
    );
    $stmt->execute([':name' => $type]);

    return true;
}

/**
 * Reads all counters
 * @return array e.g. ['attempt' => 42, 'connection' => 7]
 */
function stats_read()
{
    $data = array_fill_keys(stats_allowed_types(), 0);

    $pdo = db_connect();
    $rows = $pdo->query('SELECT name, value FROM counters')->fetchAll();
    foreach ($rows as $row) {
        $data[$row['name']] = (int) $row['value'];
    }

    return $data;
}
