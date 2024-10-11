<?php

// Get the current request URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = '/M1-MDS-2425-API'; // Base path for your project
$uri = str_replace($base_path, "", $uri);

switch ($uri) {
    case '/':
        echo json_encode(['message' => 'Welcome to the API']);
        break;

    case '/login':
        require_once './routes/login.php';
        break;

    case '/protected':
        require_once './routes/protected.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(['message' => 'Not Found']);
        break;
}