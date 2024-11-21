<?php
$config = require(__DIR__ . '/config/config.php');

$dsn = "mysql:host={$config['database_host']};dbname={$config['database_name']};charset={$config['charset']}";

try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int) $e->getCode());
}