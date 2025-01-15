<?php
$config = require(__DIR__ . '/../config/config.php');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $randomUserApiUrl = $config['user_generator_url'];
    $response = file_get_contents($randomUserApiUrl);
    $randomUserData = json_decode($response, true);

    if (!$randomUserData || !isset($randomUserData['results'][0])) {
        throw new Exception('Failed to fetch data from Random User API');
    }

    $user = $randomUserData['results'][0];
    $identity = [
        'name' => $user['name']['first'] . ' ' . $user['name']['last'],
        'gender' => $user['gender'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'cell' => $user['cell'],
        'address' => [
            'street' => $user['location']['street']['number'] . ' ' . $user['location']['street']['name'],
            'city' => $user['location']['city'],
            'state' => $user['location']['state'],
            'country' => $user['location']['country'],
            'postcode' => $user['location']['postcode'],
        ]
    ];

    echo json_encode([
        'success' => true,
        'message' => 'Fictitious identity generated successfully',
        'identity' => $identity,
    ], JSON_UNESCAPED_SLASHES);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}