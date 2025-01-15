<?php
require_once __DIR__ . '/../db_connect.php';

try {
    $stmt = $pdo->query("SELECT * FROM permissions");
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $permissions]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>