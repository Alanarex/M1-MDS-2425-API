<?php
require_once __DIR__ . '/../db_connect.php';

function logAction($username, $route, $description) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO logs (username, route, description) VALUES (:username, :route, :description)");
    $stmt->execute([
        ':username' => $username,
        ':route' => $route,
        ':description' => $description
    ]);
}
?>