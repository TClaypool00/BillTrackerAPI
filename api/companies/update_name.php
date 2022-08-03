<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_company.php';
include '../../partail_files/jwt_partial.php';

try {
    $company->company_id = set_id();
    $company->company_name = $data->companyName ?? null;

    $company->is_date_null();
    $company->format_data();
    $company->validate_data();

    if ($company->status === '') {
        $company->user_id = $decoded->userId;
        if ($company->user_has_company()) {
            if ($company->update()) {
                http_response_code(200);
                echo custom_array("company's name has been updated");
            } else {
                http_response_code(400);
                echo custom_array("Company's name could not be updated");
            }
        }  else {
            http_response_code(403);
            echo custom_array('You do not have access to this company');
        }
    } else {
        http_response_code(400);
        echo custom_array($company->status);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo custom_array($e->getMessage());
}