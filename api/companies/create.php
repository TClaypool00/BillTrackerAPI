<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_company.php';
include '../../partail_files/jwt_partial.php';

$company->company_name = $data->companyName;
$company->type_id = $data->typeId;

try {
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