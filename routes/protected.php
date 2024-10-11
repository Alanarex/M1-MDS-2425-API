<?php
require_once __DIR__ . '/../jwt/jwt_middleware.php';

$userData = validateToken();

echo json_encode(['success' => true, 'message' => 'Access granted to protected route', 'user' => $userData]);
