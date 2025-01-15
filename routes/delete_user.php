<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../helpers/logger.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username'])) {
    echo json_encode(['success' => false, 'message' => 'Username is required']);
    exit();
}

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Get user ID
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute([':username' => $data['username']]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        $pdo->rollBack();
        exit();
    }

    // Delete user permissions
    $stmt = $pdo->prepare("DELETE FROM user_permissions WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user['id']]);

    // Delete user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([':id' => $user['id']]);

    // Commit transaction
    $pdo->commit();

    // Retrieve username and URL from cookies
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'unknown';
    $url = isset($_COOKIE['url']) ? $_COOKIE['url'] : 'unknown';

    // Log the action
    logAction($username, $url, "Deleted user: {$data['username']}");

    echo json_encode(['success' => true, 'message' => 'User and related permissions deleted successfully']);
} catch (PDOException $e) {
    // Rollback transaction in case of error
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>