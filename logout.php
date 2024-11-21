<?php
setcookie("jwtToken", "", time() - 3600, "/", "", true, true);

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);