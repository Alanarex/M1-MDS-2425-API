<?php

$config = require(__DIR__ . '/../config/config.php');

if ($config) {
    if (
        !isset($config['email_validation_url'], $config['email_validation_api_key']) ||
        empty($config['email_validation_url']) || empty($config['email_validation_api_key'])
    ) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Missing or invalid config data']);
        exit();
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Config file is missing']);
    exit();
}

// Get the input data
$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data) || empty($data) || !isset($data['email']) || empty($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input or email missing']);
    exit();
}

$api_key = $config['email_validation_api_key'];
$email_address = urlencode($data['email']);
$url = $config['email_validation_url'] . "?email=" . $email_address . "&api_key=" . $api_key;

$options = [
    'http' => [
        'method' => 'GET',
        'timeout' => 10,
    ]
];
$context = stream_context_create($options);

$response = file_get_contents($url, false, $context);

if ($response === FALSE) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to connect to email validation API']);
    exit();
}

$responseArray = json_decode($response, true);

if ($responseArray && isset($responseArray['data']['status'], $responseArray['data']['result'], $responseArray['data']['email'])) {
    // Extract the fields
    $status = $responseArray['data']['status'];
    $result = $responseArray['data']['result'];
    $email = $responseArray['data']['email'];

    echo json_encode([
        'success' => true,
        'message' => [
            'email' => $email,
            'status' => $status,
            'result' => $result,
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'API returned an invalid response']);
}
