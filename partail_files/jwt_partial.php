<?php
include '../../config/Secret.php';
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$token = null;
$decoded = null;
$headers = getallheaders();
$token = $headers['Authorization'] ?? null;

if ($token !== null) {
    try {
        $decoded = JWT::decode($token, new Key(Secret::$key, Secret::$alg));
        $company->user_id = $decoded->userId;
    } catch(Exception $e) {
        http_response_code(401);
        echo custom_array($e->getMessage());
        die();
    }
} else {
    http_response_code(401);
    echo custom_array('Must have a token');
    die();
}
