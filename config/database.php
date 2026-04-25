<?php
define('DB_HOST', getenv('DB_HOST') ?: 'lamp-mysql8');
define('DB_NAME', getenv('DB_NAME') ?: 'pasanaku');
define('DB_USER', getenv('DB_USER') ?: 'docker');
define('DB_PASS', getenv('DB_PASS') ?: 'docker');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}
