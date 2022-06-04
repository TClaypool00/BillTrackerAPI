<?php
include '../../partail_files/create_header.php';
include '../../partail_files/object_partial_files/new_company.php';
include '../../global_functions.php';
include '../../config/Secret.php';
require '../../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
$headers = getallheaders();


$company->company_name = $data->companyName;
$company->type_id = $data->typeId;
$company->jwt = $data->token;

if ($company->jwt) {
    try {
        $decoded = JWT::decode($company->jwt, new Key(Secret::$key, Secret::$alg));

        $company->user_id = $decoded->userId;

        if ($company->create()) {
            http_response_code(201);
            echo custom_array('Company has been created');
        } else {
            http_response_code(400);
            echo custom_array('Company could not be created');
        }
    } catch(Exception $e) {
        http_response_code(400);
        echo custom_array($e->getMessage());
    }
}