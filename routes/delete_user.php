<?php
require_once __DIR__ . '/../db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username'])) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE username = :username");
    $stmt->execute([':username' => $data['username']]);
    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}