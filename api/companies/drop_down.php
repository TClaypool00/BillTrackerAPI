<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_company.php';
include '../../partail_files/jwt_partial.php';

if (get_isset('userId')) {
    $company->user_id = set_get_variable('userId');
} else {
    $company->user_id = null;
}

$company->validate_user_id();

if ($company->status_is_empty()) {
    if ($decoded->userId === $company->user_id) {
        $cmopany_arr = $company->drop_down();

        if (count($cmopany_arr) > 1) {
            http_response_code(200);
            echo json_encode($cmopany_arr);
        } else {
            http_response_code(404);
            echo custom_array('No companies found');
        }
    }
}