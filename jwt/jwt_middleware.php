<?php
// jwt/jwt_middleware.php
require_once __DIR__ . '/JWT.php';

function validateToken()
{
    // Check for Authorization header
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Authorization header missing']);
        exit();
    }

    error_log($_SERVER['HTTP_AUTHORIZATION']);

    // Extract token from the Authorization header (Bearer token format)
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    $jwt = str_replace('Bearer ', '', $authHeader);

    // Validate the token
    $decoded = validateJWT($jwt);
    if (!$decoded) {
        http_response_code(401); // Unauthorized
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
        exit();
    }

    // Return decoded JWT payload (user data)
    return $decoded;
}
