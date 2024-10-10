<?php
// routes/login.php
require_once __DIR__ . '/../jwt/JWT.php'; // Adjust the path if necessary

// Read the incoming POST data
$data = json_decode(file_get_contents('php://input'), true);

// Function to validate if a string is not empty, null, or white spaces
function is_valid_input($input)
{
    return isset($input) && !empty(trim($input));
}

// Check if the data array exists and is not null or empty
if (!is_array($data) || empty($data)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input, no data provided']);
    exit();
}

// Check if 'username' and 'password' keys exist and are valid
if (!array_key_exists('username', $data) || !array_key_exists('password', $data)) {
    echo json_encode(['success' => false, 'message' => 'Username or password key missing']);
    exit();
}

if (!is_valid_input($data['username']) || !is_valid_input($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Username or password cannot be empty or null']);
    exit();
}

// Dummy authentication logic
if ($data['username'] === 'admin' && $data['password'] === 'password') {
    $payload = [
        'user' => $data['username'],
        'exp' => time() + 3600
    ];
    $jwt = createJWT($payload);
    echo json_encode(['success' => true, 'token' => $jwt]);
} else {
    echo json_encode(['success' => false, 'message' => 'Access Denied']);
}
