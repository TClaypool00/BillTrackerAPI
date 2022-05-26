<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../global_functions.php';

$bill->bill_id = set_id();
$amount = $data->amount;

if ($bill->pay($amount, $bill->bill_id, 1)) {
    http_response_code(200);
    echo custom_array('Bill has been paid');
} else {
    http_response_code(400);
    echo custom_array('Bill could not be paid');
}