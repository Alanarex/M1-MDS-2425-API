<?php

// Get the current request URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = '/M1-MDS-2425-API'; // Base path for your project
$uri = str_replace($base_path, "", $uri);

error_log('url: ' . $uri . '<br>');

if ($uri === '/') {
    echo json_encode(['message' => 'Welcome to the API']);
} elseif ($uri === '/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once './routes/login.php';
} elseif ($uri === '/protected') {
    require_once './routes/protected.php'; // This route is protected by JWT
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}

// If no route matches, return 404
http_response_code(404);
echo json_encode(['message' => 'Not Found']);
