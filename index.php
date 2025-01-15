<?php

$config = require(__DIR__ . '/config/config.php');
require_once __DIR__ . '/jwt/jwt_middleware.php';
require_once __DIR__ . '/db_connect.php';

// Consolidated routes with methods and protection status
$routes = [
    '/' => [
        'file' => '/resources/index.html',
        'method' => 'GET',
        'protected' => false,
        'admin_only'=>false
    ],
    '/login' => [
        'file' => 'login.php',
        'method' => 'POST',
        'protected' => false,
        'admin_only'=>false
    ],
    // '/test' => [
    //     'file' => 'test.php',
    //     'method' => 'GET',
    //     'protected' => true,
    //     'admin_only'=>false
    // ],
    '/logout' => [
        'file' => 'logout.php',
        'method' => 'POST',
        'protected' => true,
        'admin_only'=>false
    ],
    '/migration' => [
        'file' => 'migration.php',
        'method' => 'GET',
        'protected' => false,
        'admin_only'=>false
    ],
    '/users/new' => [
        'file' => 'create_user.php',
        'method' => 'POST',
        'protected' => true,
        'admin_only'=>true
    ],
    '/users/edit' => [
        'file' => 'modify_user.php',
        'method' => 'PUT',
        'protected' => true,
        'admin_only'=>true
    ],
    '/users/delete' => [
        'file' => 'delete_user.php',
        'method' => 'DELETE',
        'protected' => true,
        'admin_only'=>true
    ],
    '/users/all' => [
        'file' => 'get_all_users.php',
        'method' => 'GET',
        'protected' => true,
        'admin_only'=>true
    ],
    '/email-validation' => [
        'file' => 'email_validation.php',
        'method' => 'POST',
        'protected' => true,
        'admin_only'=>true
    ],
    '/check-common-password' => [
        'file' => 'check_common_password.php',
        'method' => 'POST',
        'protected' => true,
        'admin_only'=>true
    ],
    '/fetch-subdomains' => [
        'file' => 'fetch_subdomains.php',
        'method' => 'POST',
        'protected' => true,
        'admin_only'=>true
    ],
    '/simulate-ddos' => [
        'file' => 'simulate_ddos.php',
        'method' => 'POST',
        'protected' => true,
        'admin_only'=>true
    ],
    '/generate-image' => [
        'file' => 'generate_random_photo.php',
        'method' => 'POST',
        'protected' => true,
        'admin_only'=>true
    ],
    '/generate-identity' => [
        'file' => 'generate_fake_identity.php',
        'method' => 'GET',
        'protected' => true,
        'admin_only'=>true
    ],
    '/crawl-person-info' => [
        'file' => 'person_info_crawler.php',
        'method' => 'POST',
        'protected' => true,
        'admin_only'=>true
    ],
    '/generate-password' => [
        'file' => 'generate_password.php',
        'method' => 'POST',
        'protected' => true,
        'admin_only'=>true
    ],
    '/email-spammer' => [
        'file' => 'email_spammer.php',
        'method' => 'POST',
        'protected' => true,
        'admin_only'=>true
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
        $user = validateToken(); // Validate the JWT token and get user data

        if (!$user) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'Unauthorized']);
            exit();
        }

        // Check if the route is admin only
        if ($route['admin_only'] && !$user['is_admin']) {
            http_response_code(403); // Forbidden
            echo json_encode($user);
            echo json_encode(['error' => 'Access denied', 'is_admin' => $user['is_admin'], 'route' => $route['admin_only']]);
            exit();
        }
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
?>
