<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../partail_files/jwt_partial.php';

$bill->bill_id = set_id();
$bill->bill_name = $data->billName ?? null;
$bill->amount_due = $data->amountDue ?? null;
$bill->is_active = $data->isActive ?? null;

try {
    $bill->data_is_null();
    $bill->validate_bill_name();
    $bill->validate_amount_due();
    $bill->validate_is_active();

    if ($bill->status === '') {
        if ($bill->update()) {
            http_response_code(200);
            echo custom_array('bill has been updated');
        } else {
            http_response_code(400);
            echo custom_array('bill could not be updated');
        }    
    } else {
        http_response_code(403);
        echo custom_array($bill->status);
    }
} catch(Exception $e) {
    http_response_code(500);
    echo custom_array($e->getMessage());
}