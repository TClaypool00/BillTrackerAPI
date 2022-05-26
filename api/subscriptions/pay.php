<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../global_functions.php';

$sub->subscription_id = set_id();
$amount = $data->$amount;

if ($sub->pay($amount, $sub->subscription_id, 3)) {
    http_response_code(200);
    echo custom_array('Subscription has been paid');
} else {
    http_response_code(400);
    echo custom_array('Subscription could not bee paid');
}