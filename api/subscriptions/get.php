<?php
include '../../partail_files/get_header.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../global_functions.php';


$sub->subscription_id = set_id();

$sub->get();

if ($sub->name != null) {
    $sub_arr = array(
        'subscriptionId' => $sub->subscription_id,
        'name' => $sub->name,
        'amountDue' => $sub->amount_due,
        'dueDate' => $sub->due_date,
        'isActive' => $sub->is_active,
        'companyId' => $sub->company_id,
        'companyName' => $sub->company_name,
        'userId' => $sub->user_id,
        'firstName' => $sub->user_first_name,
        'lastName' => $sub->user_last_name
    );

    http_response_code(200);
    print_r(json_encode($sub_arr));
} else {
    http_response_code(404);
    echo custom_array('no subcription found');
}