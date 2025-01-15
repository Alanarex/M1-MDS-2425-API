<?php
require_once __DIR__ . '/../helpers/logger.php';
require_once __DIR__ . '/../db_connect.php';

try {
    $stmt = $pdo->query("SELECT * FROM permissions");
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retrieve username and URL from cookies
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'unknown';
    $url = isset($_COOKIE['url']) ? $_COOKIE['url'] : 'unknown';

    // Log the action
    logAction($username, $url, "Fetch all permissions");

    echo json_encode(['success' => true, 'data' => $permissions]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>