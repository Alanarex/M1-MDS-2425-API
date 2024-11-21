<?php

$protectedRoutes = [
    '/test' => 'test.php',
    '/logout' => 'test.php',
    '/migration' => 'migration.php',
    '/user/new' => 'create_user.php',
    '/user/edit' => 'modify_user.php',
    '/user/delete' => 'delete_user.php',
    '/email-validation' => 'email_validation.php'
];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = '/M1-MDS-2425-API';
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
} else {
    switch ($uri) {
        case '/':
            require_once __DIR__ . '/index.html';
            break;

        case '/login':
            require_once './routes/login.php';
            break;

        default:
            http_response_code(404);
            echo json_encode(['message' => 'Not Found']);
            break;
    }
}