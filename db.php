<?php
define('DB_HOST', 'sql308.infinityfree.com');
define('DB_NAME', 'if0_42159439_riichi');
define('DB_USER', 'if0_42159439');
define('DB_PASS', 'Q5PohExa1g');

function getDB() {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    return $pdo;
}
