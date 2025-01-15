<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../helpers/logger.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Permission ID is required']);
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM permissions WHERE id = :id");
    $stmt->execute([':id' => $data['id']]);

    // Retrieve username and URL from cookies
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'unknown';
    $url = isset($_COOKIE['url']) ? $_COOKIE['url'] : 'unknown';

    // Log the action
    logAction($username, $url, "Deleted permission with ID: {$data['id']}");

    echo json_encode(['success' => true, 'message' => 'Permission deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>