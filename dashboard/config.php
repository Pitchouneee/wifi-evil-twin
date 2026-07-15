<?php
return [
    'host'    => getenv('DB_HOST') ?: '127.0.0.1',
    'port'    => (int) (getenv('DB_PORT') ?: 3306),
    'dbname'  => getenv('DB_NAME') ?: 'honeypot',
    'user'    => getenv('DB_USER') ?: 'honeypot',
    'pass'    => getenv('DB_PASS') ?: '',
    'charset' => 'utf8mb4',
];
