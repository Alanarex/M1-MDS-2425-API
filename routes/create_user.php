<?php
require_once __DIR__ . '/../db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username'], $data['password'], $data['role'])) {
    echo json_encode(['success' => false, 'message' => 'Missing user data']);
    exit();
}

$options = [
    'cost' => 12,
];

$hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT, $options);

try {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $stmt->execute([
        ':username' => $data['username'],
        ':password' => $hashedPassword,
        ':role' => $data['role']
    ]);
    echo json_encode(['success' => true, 'message' => 'User created successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}