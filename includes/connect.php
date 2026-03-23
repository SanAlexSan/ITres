<?php
// includes/connect.php

// Подключаем автозагрузчик composer
require_once __DIR__ . '/../vendor/autoload.php';

// Загружаем .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

try {
    $dsn = "pgsql:host=" . $_ENV['DB_HOST'] .
    ";port=" . $_ENV['DB_PORT'] .
    ";dbname=" . $_ENV['DB_NAME'] .
    ";sslmode=prefer";

    $pdo = new PDO(
        $dsn,
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    
    // Устанавливаем атрибуты отдельно
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    http_response_code(500);
    die("Ошибка подключения к базе: ". htmlspecialchars($e->getMessage()));
}
?>