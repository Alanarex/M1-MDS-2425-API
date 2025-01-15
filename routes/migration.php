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

    $sql = "CREATE TABLE IF NOT EXISTS permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE
    )";
    $pdo->exec($sql);

    $sql = "CREATE TABLE IF NOT EXISTS user_permissions (
        user_id INT NOT NULL,
        permission_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (permission_id) REFERENCES permissions(id),
        PRIMARY KEY (user_id, permission_id)
    )";
    $pdo->exec($sql);

    // Insert admin user
    $username = 'admin';
    $password = password_hash('password', PASSWORD_DEFAULT); 
    $is_admin = true;

    $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (:username, :password, :is_admin)");
    $stmt->execute(['username' => $username, 'password' => $password, 'is_admin' => $is_admin]);

    $permissions = ['admin', 'view_users', 'edit_users', 'delete_users', 'create_users', 'hackr', 'view_permissions', 'delete_permissions', 'add_permissions'];
    $stmt = $pdo->prepare("INSERT INTO permissions (name) VALUES (:name)");

    foreach ($permissions as $permission) {
        $stmt->execute([':name' => $permission]);
    }

    echo "Migration completed successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>