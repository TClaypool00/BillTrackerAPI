<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../global_functions.php';

$sub->subscription_id = set_id();
$amount = $data->amount;

if ($sub->sub_exists()) {
    if ($sub->is_paid($sub->subscription_id)) {
        http_response_code(200);
        echo custom_array('Subscription has already been paid');
    } else {
        if ($sub->pay($amount, $sub->subscription_id, 3)) {
            http_response_code(200);
            echo custom_array('Subscription has already been paid');
        } else {
            http_response_code(400);
            echo custom_array('Subscription could not be paid');
        }
    }
}