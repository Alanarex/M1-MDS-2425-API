<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../helpers/logger.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username'])) {
    echo json_encode(['success' => false, 'message' => 'Missing username']);
    exit();
}

try {
    // Check if the user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $data['username']]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }

    // Prepare the update query
    $updateFields = [];
    $updateParams = [':username' => $data['username']];

    if (isset($data['password'])) {
        $updateFields[] = 'password = :password';
        $updateParams[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }

    if (isset($data['is_admin'])) {
        $updateFields[] = 'is_admin = :is_admin';
        $updateParams[':is_admin'] = $data['is_admin'];
    }

    if (!empty($updateFields)) {
        $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($updateParams);
    }

    // Sync permissions
    if (isset($data['permissions']) && is_array($data['permissions'])) {
        // Get all permission IDs from the database
        $stmt = $pdo->query("SELECT id, name FROM permissions");
        $permissions = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Get current user permissions
        $stmt = $pdo->prepare("SELECT permission_id FROM user_permissions WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $user['id']]);
        $currentPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Determine permissions to add and remove
        $newPermissions = array_intersect_key($permissions, array_flip($data['permissions']));
        $newPermissionIds = array_values($newPermissions);
        $permissionsToAdd = array_diff($newPermissionIds, $currentPermissions);
        $permissionsToRemove = array_diff($currentPermissions, $newPermissionIds);

        // Add new permissions
        if (!empty($permissionsToAdd)) {
            $stmt = $pdo->prepare("INSERT INTO user_permissions (user_id, permission_id) VALUES (:user_id, :permission_id)");
            foreach ($permissionsToAdd as $permissionId) {
                $stmt->execute([':user_id' => $user['id'], ':permission_id' => $permissionId]);
            }
        }

        // Remove old permissions
        if (!empty($permissionsToRemove)) {
            $stmt = $pdo->prepare("DELETE FROM user_permissions WHERE user_id = :user_id AND permission_id = :permission_id");
            foreach ($permissionsToRemove as $permissionId) {
                $stmt->execute([':user_id' => $user['id'], ':permission_id' => $permissionId]);
            }
        }
    }

    // Retrieve username and URL from cookies
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'unknown';
    $url = isset($_COOKIE['url']) ? $_COOKIE['url'] : 'unknown';

    // Log the action
    logAction($username, $url, "Modified user: {$data['username']}");

    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>