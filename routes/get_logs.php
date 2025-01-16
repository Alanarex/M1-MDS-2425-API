<?php
require_once __DIR__ . '/../db_connect.php';

try {
    // Get all logs
    $stmt = $pdo->prepare("SELECT * FROM logs");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'Logs retrieved successfully', 
        'data' => $logs,
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
    ]);
    http_response_code(500);
}
?>
