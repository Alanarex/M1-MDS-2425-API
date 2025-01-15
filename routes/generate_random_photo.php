<?php
$config = require(__DIR__ . '/../config/config.php');

$data = json_decode(file_get_contents('php://input'), true);

try {
    $randomUserApiUrl = $config['user_generator_url'];
    $response = file_get_contents($randomUserApiUrl);
    $randomUserData = json_decode($response, true);

    if (!$randomUserData || !isset($randomUserData['results'][0]['picture']['large'])) {
        throw new Exception('Failed to fetch user data from Random User API');
    }

    $photoUrl = $randomUserData['results'][0]['picture']['large'];
    echo json_encode([
        'success' => true,
        'message' => 'User created successfully',
        'user' => [
            'photo' => $photoUrl,
        ],
    ], JSON_UNESCAPED_SLASHES);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
