<?php
// Fill these in with your Hostinger MySQL credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'riichi');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

function getDB() {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    return $pdo;
}
