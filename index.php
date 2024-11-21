<?php

$config = require(__DIR__ . '/config/config.php');

$protectedRoutes = [
    '/test' => 'test.php',
    '/logout' => 'test.php',
    '/migration' => 'migration.php',
    '/user/new' => 'create_user.php',
    '/user/edit' => 'modify_user.php',
    '/user/delete' => 'delete_user.php',
    '/email-validation' => 'email_validation.php',
    '/check-common-password' => 'check_common_password.php',
    '/fetch-subdomains' => 'fetch_subdomains.php',
];

$publicRoutes = [
    '/' => '/resources/index.html',
    '/login' => 'login.php',
];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = $config['base_path'];
$uri = str_replace($base_path, "", $uri);

if (array_key_exists($uri, $protectedRoutes)) {
    require_once __DIR__ . '/jwt/jwt_middleware.php';
    validateToken();
    $controllerFile = __DIR__ . '/routes/' . $protectedRoutes[$uri];
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Controller file not found']);
    }
} elseif (array_key_exists($uri, $publicRoutes)) {

    if ($uri === '/') {
        require_once __DIR__ . $publicRoutes[$uri];
    } else {
        $controllerFile = __DIR__ . '/routes/' . $publicRoutes[$uri];
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Controller file not found']);
        }
    }
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Not Found']);
}
