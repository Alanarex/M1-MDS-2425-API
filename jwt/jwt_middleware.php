<?php
// jwt/jwt_middleware.php
require_once __DIR__ . '/JWT.php';


function validateToken()
{
    // Get the Authorization header
    if (!isset($_COOKIE['jwtToken'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token missing']);
        exit();
    }

    $jwt = $_COOKIE['jwtToken'];
    $decoded = validateJWT($jwt);
    error_log('decoded : ' . var_dump($decoded));
    if (!$decoded) {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
        exit();
    }

    // Return decoded JWT payload (user data)
    return $decoded;
}
