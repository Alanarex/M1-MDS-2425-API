<?php
require_once __DIR__ . '/../helpers/logger.php';
setcookie("jwtToken", "", time() - 3600, "/", "", true, true);

// Retrieve username and URL from cookies
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'unknown';
$url = isset($_COOKIE['url']) ? $_COOKIE['url'] : 'unknown';

// Log the action
logAction($username, $url, "User logged out");

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);