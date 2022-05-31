<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../global_functions.php';

$bill->bill_id = set_id();
$amount = $data->amount;

if ($bill->bill_exists()) {
    if ($bill->is_paid($bill->bill_id)) {
        http_response_code(200);
        echo custom_array('Bill has already been paid');
    } else {
        if ($bill->pay($amount, $bill->bill_id, 1)) {
            http_response_code(200);
            echo custom_array('Bill has already been paid');
        } else {
            http_response_code(400);
            echo custom_array('Bill could not be paid');
        }
    }
} else {
    http_response_code(404);
    echo custom_array('Bill with an id of ' . $bill->bill_id . ' could not be found.');
}