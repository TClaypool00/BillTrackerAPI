<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_company.php';
include '../../partail_files/jwt_partial.php';

$company->company_name = $data->companyName ?? null;

$company->data_is_null();
$company->format_data();
$company->validate_data();

try {
    if ($company->status === '') {
        $company->user_id = $decoded->userId;
        if ($company->create()) {
            http_response_code(201);
            echo custom_array('Company has been created');
        } else {
            http_response_code(400);
            echo custom_array('Company could not be created');
        }
    } else {
        http_response_code(400);
        echo custom_array($company->status);
    }
} catch(Exception $e) {
    http_response_code(500);
    echo custom_array($e->getMessage());
}