<?php
// config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u82083');
define('DB_USER', 'u82083');
define('DB_PASS', '5977399'); // ВАЖНО: введите свой пароль от MySQL

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}
?>
