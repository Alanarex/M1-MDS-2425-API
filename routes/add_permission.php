<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../helpers/logger.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name'])) {
    echo json_encode(['success' => false, 'message' => 'Permission name is required']);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO permissions (name) VALUES (:name)");
    $stmt->execute([':name' => $data['name']]);

    // Retrieve username and URL from cookies
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'unknown';
    $url = isset($_COOKIE['url']) ? $_COOKIE['url'] : 'unknown';

    // Log the action
    logAction($username, $url, "Added new permission: {$data['name']}");

    echo json_encode(['success' => true, 'message' => 'Permission added successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>