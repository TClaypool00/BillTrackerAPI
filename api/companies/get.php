<?php
include '../../partail_files/get_header.php';
include '../../partail_files/object_partial_files/new_company.php';
include '../../global_functions.php';

$company->company_id = set_id();

$company->get();

if ($company->company_name != null) {
    $company_arr = array(
        'companyId' => $company->company_id,
        'companyName' => $company->company_name,
        'isActive' => $company->is_active,
        'typeId' => $company->type_id,
        'typeName' => $company->type_name,
        'firstName' => $company->user_first_name,
        'lastName' => $company->user_last_name
    );

    http_response_code(200);
    print_r(json_encode($company_arr));
} else {
    http_response_code(404);
    echo custom_array('No company found.');
}