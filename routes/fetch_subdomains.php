<?php
require_once __DIR__ . '/../config/config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['domain']) || empty($data['domain'])) {
    echo json_encode(['success' => false, 'message' => 'Domain name is required']);
    http_response_code(400);
    exit();
}

$domain = $data['domain'];
$apiKey = $config['securitytrails_api_key'];
$urlTemplate = $config['securitytrails_url'];

$url = str_replace('$domain', $domain, $urlTemplate);

$options = [
    "http" => [
        "header" => "APIKEY: $apiKey\r\n"
    ]
];
$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'Unable to fetch subdomains']);
    http_response_code(500);
    exit();
}

$data = json_decode($response, true);

if (isset($data['subdomains'])) {
    echo json_encode(['success' => true, 'message' => 'Subdomains found', 'subdomains' => $data['subdomains']]);
} else {
    echo json_encode(['success' => false, 'message' => 'No subdomains found']);
}
