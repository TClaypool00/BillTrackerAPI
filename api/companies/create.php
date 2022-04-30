<?php
include '../../partail_files/create_header.php';
include '../../partail_files/object_partial_files/new_company.php';
include '../../global_functions.php';

$company->company_name = $data->companyName;
$company->user_id = $data->userId;
$company->type_id = $data->typeId;

if ($company->create()) {
    http_response_code(201);
    echo custom_array('Company has been created');
} else {
    http_response_code(400);
    echo custom_array('Company could not be created');
}