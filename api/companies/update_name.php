<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_company.php';
include '../../global_functions.php';

$company->company_id = set_id();
$company->company_name = $data->companyName;

if ($company->update()) {
    http_response_code(200);
    echo custom_array("company's name has been updated");
} else {
    http_response_code(400);
    echo custom_array("Company's name could not be updated");
}