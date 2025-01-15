<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../helpers/logger.php';

try {
    // Get all users
    $stmt = $pdo->prepare("SELECT id, username, is_admin FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get permissions for each user
    foreach ($users as &$user) {
        $stmt = $pdo->prepare("
            SELECT p.name 
            FROM permissions p
            JOIN user_permissions up ON p.id = up.permission_id
            WHERE up.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $user['id']]);
        $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $user['permissions'] = $permissions;
    }

    // Retrieve username and URLs from cookies
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'unknown';
    $url = isset($_COOKIE['url']) ? $_COOKIE['url'] : 'unknown';

    //  Log the action
    logAction($username, $url, "Retrieved all users");

    echo json_encode([
        'success' => true,
        'message' => 'Users retrieved successfully', 
        'data' => $users,
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
    ]);
    http_response_code(500);
}
?>
