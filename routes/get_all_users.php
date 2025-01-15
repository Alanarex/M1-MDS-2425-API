<?php

try {
    $stmt = $pdo->prepare("SELECT id, username, is_admin FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll();

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
