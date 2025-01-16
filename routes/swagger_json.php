<?php
header('Content-Type: application/json');
$swaggerFile = __DIR__ . '/../resources/swagger.json';

if (file_exists($swaggerFile)) {
    echo file_get_contents($swaggerFile);
} else {
    http_response_code(404);
    echo json_encode(['message' => 'Swagger file not found']);
}
