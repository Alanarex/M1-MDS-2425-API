<?php
require_once __DIR__ . '/../jwt/JWT.php';
require __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../helpers/logger.php';

$data = json_decode(file_get_contents('php://input'), true);

function is_valid_input($input)
{
    return isset($input) && !empty(trim($input));
}

if (!is_array($data) || empty($data)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input, no data provided']);
    exit();
}

if (!array_key_exists('username', $data) || !array_key_exists('password', $data)) {
    echo json_encode(['success' => false, 'message' => 'Username or password key missing']);
    exit();
}

if (!is_valid_input($data['username']) || !is_valid_input($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Username or password cannot be empty or null']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $data['username']]);
    $user = $stmt->fetch();

    // Verify user and check if the user is 'admin'
    if ($user) {
        if (password_verify($data['password'], $user['password'])) {
            // Get user permissions
            $stmt = $pdo->prepare("
                SELECT p.name 
                FROM permissions p
                JOIN user_permissions up ON p.id = up.permission_id
                WHERE up.user_id = :user_id
            ");
            $stmt->execute([':user_id' => $user['id']]);
            $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $payload = [
                'user' => $user['username'],
                'is_admin' => $user['is_admin'],
                'permissions' => $permissions,
                'exp' => time() + 3600
            ];
            $jwt = createJWT($payload);

            // Log the action
            logAction($user['username'], "/login", "Logged in successfully");

            // Send the response
            echo json_encode(['success' => true, 'token' => $jwt]);
            // Set the JWT in a secure HttpOnly cookie
            setcookie("jwtToken", $jwt, time() + 3600, "/", "", true, true);
        } else {
            echo json_encode(['success' => false, 'message' => 'Password mismatch']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
