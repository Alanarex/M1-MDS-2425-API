<?php
require_once __DIR__ . '/../db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username'])) {
    echo json_encode(['success' => false, 'message' => 'Missing username']);
    exit();
}

try {
    // Check if the user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $data['username']]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }

    // Prepare the update query
    $updateFields = [];
    $updateParams = [':username' => $data['username']];

    if (isset($data['password'])) {
        $updateFields[] = 'password = :password';
        $updateParams[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }

    if (isset($data['is_admin'])) {
        $updateFields[] = 'is_admin = :is_admin';
        $updateParams[':is_admin'] = $data['is_admin'];
    }

    if (!empty($updateFields)) {
        $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($updateParams);
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}