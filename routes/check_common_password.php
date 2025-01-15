<?php
$config = require(__DIR__ . '/../config/config.php');
require_once __DIR__ . '/../helpers/logger.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['password']) || empty($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    http_response_code(400);
    exit();
}

$password = $data['password'];
$fileUrl = $config['commond_passwords_file_url'];
$fileContent = file_get_contents($fileUrl);
if ($fileContent === false) {
    echo json_encode(['success' => false, 'message' => 'Unable to fetch the password list file']);
    http_response_code(500);
    exit();
}

$passwordList = explode("\n", $fileContent);
$passwordList = array_map('trim', $passwordList);

$index = array_search($password, $passwordList);

if ($index !== false) {
    // Retrieve username and URL from cookies
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'unknown';
    $url = isset($_COOKIE['url']) ? $_COOKIE['url'] : 'unknown';

    // Log the action
    logAction($username, $url, "Checked common password: {$data['password']}");

    echo json_encode(['success' => true, 'message' => 'Password found', 'index' => $index]);
} else {
    echo json_encode(['success' => false, 'message' => 'Password not found']);
}

