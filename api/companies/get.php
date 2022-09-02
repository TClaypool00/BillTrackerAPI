<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_company.php';
include '../../partail_files/jwt_partial.php';

try {
    $company->company_id = set_id();
    $company->user_id = $decoded->userId;

    if (!$decoded->isAdmin && !$company->user_has_company()) {
        echo custom_array(Company::$not_auth);
        die();
    }

    $company->get();

    if ($company->company_name != null) {
        $company_arr = array(
            'companyId' => $company->company_id,
            'companyName' => $company->company_name,
            'isActive' => $company->is_active,
            'typeId' => $company->type_id,
            'typeName' => $company->type_name,
            'userId' => $company->user_id,
            'firstName' => $company->user_first_name,
            'lastName' => $company->user_last_name  
        );

        http_response_code(200);
        print_r(json_encode($company_arr));
    } else {
        http_response_code(404);
        echo custom_array('No company found.');
    }
} catch (Throwable $e) {
    http_response_code(500);
    $company->createError($e);
    echo custom_array($company->err_message);
}