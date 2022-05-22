<?php
include '../../partail_files/create_header.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../global_functions.php';

$sub->name = $data->name;
$sub->amount_due = $data->amountDue;
$sub->due_date = $data->dueDate;
$sub->company_id = $data->companyId;
$sub->due_date = $data->dueDate;

if ($sub->create()) {
    http_response_code(201);
    echo custom_array('Subscription has been created');
} else {
    http_response_code(400);
    echo custom_array('Subscription could not be created');
}