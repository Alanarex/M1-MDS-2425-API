<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$config = require(__DIR__ . '/config/config.php');
require_once __DIR__ . '/jwt/jwt_middleware.php';
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/helpers/logger.php';

// Define your routes and their properties
$routes = [
    '/' => [
        'file' => '/resources/index.html',
        'method' => 'GET',
        'protected' => false,
        'permissions' => [],
        'admin_only' => false
    ],
    '/login' => [
        'file' => 'login.php',
        'method' => 'POST',
        'protected' => false,
        'permissions' => [],
        'admin_only' => false
    ],
    '/logout' => [
        'file' => 'logout.php',
        'method' => 'POST',
        'protected' => true,
        'permissions' => [],
        'admin_only' => false
    ],
    '/migration' => [
        'file' => 'migration.php',
        'method' => 'GET',
        'protected' => false,
        'permissions' => [],
        'admin_only' => false
    ],
    '/users/new' => [
        'file' => 'create_user.php',
        'method' => 'POST',
        'protected' => true,
        'permissions' => ['create_users'],
        'admin_only' => false
    ],
    '/users/edit' => [
        'file' => 'modify_user.php',
        'method' => 'PUT',
        'protected' => true,
        'permissions' => ['edit_users'],
        'admin_only' => false
    ],
    '/users/delete' => [
        'file' => 'delete_user.php',
        'method' => 'DELETE',
        'protected' => true,
        'permissions' => ['delete_users'],
        'admin_only' => false
    ],
    '/users/all' => [
        'file' => 'get_all_users.php',
        'method' => 'GET',
        'protected' => true,
        'permissions' => ['view_users'],
        'admin_only' => false
    ],
    '/email-validation' => [
        'file' => 'email_validation.php',
        'method' => 'POST',
        'protected' => true,
        'permissions' => ['hackr'],
        'admin_only' => false
    ],
    '/check-common-password' => [
        'file' => 'check_common_password.php',
        'method' => 'POST',
        'protected' => true,
        'permissions' => ['hackr'],
        'admin_only' => false
    ],
    '/fetch-subdomains' => [
        'file' => 'fetch_subdomains.php',
        'method' => 'POST',
        'protected' => true,
        'permissions' => ['hackr'],
        'admin_only' => false
    ],
    '/simulate-ddos' => [
        'file' => 'simulate_ddos.php',
        'method' => 'POST',
        'protected' => true,
        'permissions' => ['hackr'],
        'admin_only' => false
    ],
    '/generate-image' => [
        'file' => 'generate_random_photo.php',
        'method' => 'POST',
        'protected' => true,
        'permissions' => ['hackr'],
        'admin_only' => false
    ],
    '/generate-identity' => [
        'file' => 'generate_fake_identity.php',
        'method' => 'GET',
        'protected' => true,
        'permissions' => ['hackr'],
        'admin_only' => false
    ],
    '/information-crawler' => [
        'file' => 'information-crawler.php',
        'method' => 'POST',
        'protected' => true,
        'permissions' => ['hackr'],
        'admin_only' => false
    ],
    '/generate-password' => [
        'file' => 'generate_password.php',
        'method' => 'POST',
        'protected' => true,
        'permissions' => ['hackr'],
        'admin_only' => false
    ],
    '/email-spammer' => [
        'file' => 'email_spammer.php',
        'method' => 'POST',
        'protected' => true,
        'permissions' => ['hackr'],
        'admin_only' => false
    ],
    '/permissions' => [
        'file' => 'get_permissions.php',
        'method' => 'GET',
        'protected' => true,
        'permissions' => ['view_permissions'],
        'admin_only' => false
    ],
    '/permissions/new' => [
        'file' => 'add_permission.php',
        'method' => 'POST',
        'protected' => true,
        'permissions' => ['add_permissions'],
        'admin_only' => false
    ],
    '/permissions/delete' => [
        'file' => 'delete_permission.php',
        'method' => 'DELETE',
        'protected' => true,
        'permissions' => ['delete_permissions'],
        'admin_only' => false
    ],
    '/logs/all' => [
        'file' => 'get_logs.php',
        'method' => 'GET',
        'protected' => true,
        'permissions' => [],
        'admin_only' => true
    ],
    '/swagger.json' => [
        'file' => 'swagger.php',
        'method' => 'GET',
        'protected' => false,
        'permissions' => [],
        'admin_only' => false
    ]
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

        if(isset($route['admin_only']) && $route['admin_only'] && !$user['is_admin']) {
            http_response_code(403); // Forbidden
            echo json_encode(['error' => 'Access denied or not authorized']);
            exit();
        }

        // Check if the user is admin
        if (!$user['is_admin']) {
            // Check if the user has the required permission
            if (isset($route['permissions']) && !empty($route['permissions'])) {
                $hasPermission = false;
                foreach ($route['permissions'] as $permission) {
                    if (in_array($permission, $user['permissions'])) {
                        $hasPermission = true;
                        break;
                    }
                }

                if (!$hasPermission) {
                    http_response_code(403); // Forbidden
                    echo json_encode(['error' => 'Access denied or not authorized']);
                    exit();
                }
            }
        }
        
        // Add the user's username and URL to the cookies
        if(isset($user['username']))
            setcookie("username", $user['username'], time() + 3600, "/", "", true, true);
        if(isset($uri))
            setcookie("url", $uri, time() + 3600, "/", "", true, true);
        
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
