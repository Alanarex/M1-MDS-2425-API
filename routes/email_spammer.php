<?php
require_once __DIR__ . '/../jwt/jwt_middleware.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../helpers/logger.php';
validateToken();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['to'], $data['subject'], $data['message'], $data['count'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields: to, subject, message, count']);
    http_response_code(400);
    exit();
}

$to = $data['to'];
$subject = $data['subject'];
$message = $data['message'];
$count = (int) $data['count'];

if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    http_response_code(400);
    exit();
}

if ($count <= 0 || $count > 100) { 
    echo json_encode(['success' => false, 'message' => 'Count must be between 1 and 100']);
    http_response_code(400);
    exit();
}

try {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPDebug  = 2; 
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "tls";                 
    $mail->Username = $config['spammer_email']; 
    $mail->Password = $config['spammer_password']; 
    $mail->Port = 587;

    $mail->setFrom("tototiti@mail.xx", 'potit lion'); 
    $mail->AddAddress($to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;

    for ($i = 0; $i < $count; $i++) {
        if(!$mail->Send()) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $mail->ErrorInfo]);
            http_response_code(500);
            exit();
          } else {
            echo "Message sent!";
          }
    }

    // Retrieve username and URL from cookies
    $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : 'unknown';
    $url = isset($_COOKIE['url']) ? $_COOKIE['url'] : 'unknown';

    // Log the action
    logAction($username, $url, "Spammed email: {$data['email']} with content: {$data['content']} for {$data['count']} times");


    echo json_encode(['success' => true, 'message' => "Successfully sent {$count} emails to {$to}"]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $mail->ErrorInfo]);
    http_response_code(500);
}
