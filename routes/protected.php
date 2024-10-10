<?php
// routes/protected.php
require_once __DIR__ . '/../jwt/jwt_middleware.php';

// Call the middleware to validate the JWT
$userData = validateToken();

// Proceed with the protected route logic
echo json_encode(['success' => true, 'message' => 'Access granted to protected route', 'user' => $userData]);
