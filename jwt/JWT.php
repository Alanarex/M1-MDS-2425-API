<?php
// jwt/JWT.php

require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function createJWT($payload)
{
    $config = require(__DIR__ . '/../config/config.php');
    echo "creating jwt";
    return JWT::encode($payload, $config['jwt_secret_key'], $config['jwt_algorithm']);
}

function validateJWT($jwt)
{
    $config = require(__DIR__ . '/../config/config.php');
    try {
        // Decode and verify the token
        $decoded = JWT::decode($jwt, new Key($config['jwt_secret_key'], $config['jwt_algorithm']));
        return (array) $decoded;
    } catch (Exception $e) {
        return false;
    }
}
