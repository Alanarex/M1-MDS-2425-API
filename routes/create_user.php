<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../helpers/logger.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username'], $data['password'], $data['is_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Missing user data']);
    exit();
}

$options = [
    'cost' => 12,
];

$hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT, $options);

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Check if the username already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->execute([':username' => $data['username']]);
    $userExists = $stmt->fetchColumn();

    if ($userExists) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        $pdo->rollBack();
        exit();
    }

    // Insert the new user
    $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin) VALUES (:username, :password, :is_admin)");
    $stmt->execute([
        ':username' => $data['username'],
        ':password' => $hashedPassword,
        ':is_admin' => $data['is_admin']
    ]);

    // Get the new user's ID
    $userId = $pdo->lastInsertId();

    // Attach permissions if provided
    if (isset($data['permissions']) && is_array($data['permissions'])) {
        // Get all permission IDs from the database
        $stmt = $pdo->query("SELECT id, name FROM permissions");
        $permissions = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Prepare the insert statement for user_permissions
        $stmt = $pdo->prepare("INSERT INTO user_permissions (user_id, permission_id) VALUES (:user_id, :permission_id)");

        foreach ($data['permissions'] as $permissionName) {
            if (isset($permissions[$permissionName])) {
                $stmt->execute([':user_id' => $userId, ':permission_id' => $permissions[$permissionName]]);
            }
        }
    }

    // Commit transaction
    $pdo->commit();

    // Retrieve username and URL from cookies
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'unknown';
    $url = isset($_COOKIE['url']) ? $_COOKIE['url'] : 'unknown';

    // Log the action
    logAction($username, $url, "Created a new user: {$data['username']}");

    echo json_encode(['success' => true, 'message' => 'User created successfully']);
} catch (PDOException $e) {
    // Rollback transaction in case of error
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>