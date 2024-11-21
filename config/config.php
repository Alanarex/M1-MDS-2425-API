<?php

use Dotenv\Dotenv;

// Load .env file
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

return [
    'jwt_secret_key' => $_ENV['JWT_SECRET_KEY'],
    'jwt_algorithm' => $_ENV['JWT_ALGORITHM'],
    'email_validation_url' => $_ENV['EMAIL_VALIDATION_URL'],
    'email_validation_api_key' => $_ENV['EMAIL_VALIDATION_API_KEY'],
    'database_host' => $_ENV['DATABASE_HOST'],
    'database_name' => $_ENV['DATABASE_NAME'],
    'username' => $_ENV['DATABASE_USERNAME'],
    'password' => $_ENV['DATABASE_PASSWORD'],
    'charset' => 'utf8mb4',
    'base_path' => $_ENV['BASE_PATH'],
    'commond_passwords_file_url' => $_ENV['COMMON_PASSWORDS_FILE_URL'],
    'securitytrails_url' => $_ENV['SECURITYTRAILS_URL'],
    'securitytrails_api_key' => $_ENV['SECURITYTRAILS_API_KEY'],
];
