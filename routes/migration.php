<?php
require_once __DIR__ . '/../db_connect.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin BOOLEAN NOT NULL
    )";
    $pdo->exec($sql);

    // Insert admin user
    $username = 'admin';
    $password = password_hash('password', PASSWORD_DEFAULT); 
    $is_admin = true;

    $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (:username, :password, :is_admin)");
    $stmt->execute(['username' => $username, 'password' => $password, 'is_admin' => $is_admin]);

    echo "Migration completed successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>