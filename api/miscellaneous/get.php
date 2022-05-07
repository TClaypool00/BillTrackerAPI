<?php
include '../../partail_files/get_header.php';
include '../../partail_files/object_partial_files/new_miscellaneous.php';
include '../../global_functions.php';

$misc->miscellaneous_id = set_id();

$misc->get();

if ($misc->name != null) {
    $misc_arr = array(
        'miscellaneousId' => $misc->miscellaneous_id,
        'name' => $misc->name,
        'amount' => $misc->amount,
        'companyId' => $misc->company_id,
        'companyName' => $misc->company_name,
        'userId' => $misc->user_id,
        'firstName' => $misc->user_first_name,
        'lastName' => $misc->user_last_name
    );

    http_response_code(200);
    print_r(json_encode($misc_arr));
} else {
    http_response_code(404);
    echo custom_array('No Miscellaneous found');
}