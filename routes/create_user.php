<?php
require_once __DIR__ . '/../db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username'], $data['password'], $data['is_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Missing user data']);
    exit();
}

$options = [
    'cost' => 12,
];

$hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT, $options);

try {
    // Check if the username already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->execute([':username' => $data['username']]);
    $userExists = $stmt->fetchColumn();

    if ($userExists) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit();
    }

    // Insert the new user
    $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (:username, :password, :is_admin)");
    $stmt->execute([
        ':username' => $data['username'],
        ':password' => $hashedPassword,
        ':is_admin' => $data['is_admin']
    ]);
    echo json_encode(['success' => true, 'message' => 'User created successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}