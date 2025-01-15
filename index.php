<?php

$config = require(__DIR__ . '/config/config.php');

// Consolidated routes with methods and protection status
$routes = [
    '/' => [
        'file' => '/resources/index.html',
        'method' => 'GET',
        'protected' => false
    ],
    '/login' => [
        'file' => 'login.php',
        'method' => 'POST',
        'protected' => false

    ],
    '/test' => [
        'file' => 'test.php',
        'method' => 'GET',
        'protected' => true

    ],
    '/logout' => [
        'file' => 'test.php',
        'method' => 'POST',
        'protected' => true
    ],
    '/migration' => [
        'file' => 'migration.php',
        'method' => 'GET',
        'protected' => true
    ],
    '/user/new' => [
        'file' => 'create_user.php',
        'method' => 'POST',
        'protected' => true
    ],
    '/user/edit' => [
        'file' => 'modify_user.php',
        'method' => 'PUT',
        'protected' => true
    ],
    '/user/delete' => [
        'file' => 'delete_user.php',
        'method' => 'DELETE',
        'protected' => true
    ],
    '/email-validation' => [
        'file' => 'email_validation.php',
        'method' => 'POST',
        'protected' => true
    ],
    '/check-common-password' => [
        'file' => 'check_common_password.php',
        'method' => 'POST',
        'protected' => true
    ],
    '/fetch-subdomains' => [
        'file' => 'fetch_subdomains.php',
        'method' => 'POST',
        'protected' => true
    ],
    '/simulate-ddos' => [
        'file' => 'simulate_ddos.php',
        'method' => 'POST',
        'protected' => true
    ],
    '/generate-image' => [
        'file' => 'generate_random_photo.php',
        'method' => 'POST',
        'protected' => true
    ],
    '/generate-identity' => [
        'file' => 'generate_fake_identity.php',
        'method' => 'GET',
        'protected' => true
    ],
    '/crawl-person-info' => [
        'file' => 'person_info_crawler.php',
        'method' => 'POST',
        'protected' => true
    ],
    '/generate-password' => [
        'file' => 'generate_password.php',
        'method' => 'POST',
        'protected' => true
    ],
    '/email-spammer' => [
        'file' => 'email_spammer.php',
        'method' => 'POST',
        'protected' => true
    ],
];

// Parse the request URI and method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = rtrim($scriptName, '/');
$uri = preg_replace('#^' . preg_quote($basePath, '#') . '#', '', $uri);

// Check if the route exists
if (array_key_exists($uri, $routes)) {
    $route = $routes[$uri];

    // Validate HTTP method
    if ($method !== $route['method']) {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
        exit();
    }

    // Check if the route is protected
    if ($route['protected']) {
        require_once __DIR__ . '/jwt/jwt_middleware.php';
        validateToken(); // Validate the JWT token
    }

    // Include the controller file or resource
    $controllerFile = $uri == '/' ? __DIR__ . ($route['file']) : __DIR__ . '/routes/' . ($route['file']);
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Controller file not found']);
    }
} else {
    // Route not found
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}
