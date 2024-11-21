<?php
require_once __DIR__ . '/../db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['username'], $data['role'])) {
    echo json_encode(['success' => false, 'message' => 'Missing user data']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE users SET username = :username, role = :role WHERE id = :id");
    $stmt->execute([
        ':id' => $data['id'],
        ':username' => $data['username'],
        ':role' => $data['role']
    ]);
    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}