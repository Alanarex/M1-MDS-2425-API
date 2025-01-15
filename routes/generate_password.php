<?php
require_once __DIR__ . '/../config/config.php';

$data = json_decode(file_get_contents('php://input'), true);

$length = isset($data['length']) ? (int) $data['length'] : 16;
$special = isset($data['special']) ? (bool) $data['special'] : true;
$uppercase = isset($data['uppercase']) ? (bool) $data['uppercase'] : true;
$numbers = isset($data['numbers']) ? (bool) $data['numbers'] : true;

try {
    $passwordApiUrl = $config['password_generator_api_url'];
    $passwordApiUrl .= "?length={$length}&special={$special}&upper={$uppercase}&numbers={$numbers}";

    $response = file_get_contents($passwordApiUrl);
    $passwordData = json_decode($response, true);

    if (!$passwordData || !isset($passwordData[0]['password'])) {
        throw new Exception('Failed to fetch password from Passwordwolf API');
    }

    $password = $passwordData[0]['password'];

    echo json_encode([
        'success' => true,
        'message' => 'Password generated successfully',
        'password' => $password,
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    http_response_code(500);
}
