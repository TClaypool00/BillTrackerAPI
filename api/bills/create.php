<?php
include '../../partail_files/create_header.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../global_functions.php';

$bill->bill_name = $data->billName;
$bill->amount_due = $data->amountDue;
$bill->is_recurring = $data->isRecurring;
$bill->user_id = $data->userId;
$bill->end_date = $data->endDate;

if ($bill->create()) {
    http_response_code(201);
    echo custom_array('Bill has been created');
} else {
    http_response_code(400);
    echo custom_array('bill could not be created');
}