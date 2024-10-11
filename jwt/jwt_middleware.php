<?php
require_once __DIR__ . '/JWT.php';

function validateToken()
{
    if (!isset($_COOKIE['jwtToken'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token missing']);
        exit();
    }

    $jwt = $_COOKIE['jwtToken'];
    $decoded = validateJWT($jwt);
    if (!$decoded) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
        exit();
    }

    return $decoded;
}
