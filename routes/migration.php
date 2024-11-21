<?php
require_once __DIR__ . '/../db_connect.php';

try {
    // SQL query to create the `users` table
    $createTableQuery = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;
    ";

    // Execute the query
    $pdo->exec($createTableQuery);
    echo "Table `users` created successfully.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}