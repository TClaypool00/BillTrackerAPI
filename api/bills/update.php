<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../global_functions.php';

$bill->bill_id = set_id();
$bill->bill_name = $data->billName;
$bill->amount_due = $data->amountDue;
$bill->is_active = $data->isActive;

if ($bill->update()) {
    http_response_code(200);
    echo custom_array('bill has been updated');
} else {
    http_response_code(400);
    echo custom_array('bill could not be updated');
}
