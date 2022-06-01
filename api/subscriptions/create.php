<?php
include '../../partail_files/create_header.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../global_functions.php';

$sub->name = $data->name ?? null;
$sub->amount_due = $data->amountDue ?? null;
$sub->date_due = $data->dueDate ?? null;
$sub->company_id = $data->companyId ?? null;

$sub->is_date_null();
$sub->format_data();
$sub->validate_data();

if ($sub->status === '') {
    if ($sub->create()) {
        http_response_code(201);
        echo custom_array('Subscription has been created');
    } else {
        http_response_code(400);
        echo custom_array('Subscription could not be created');
    }
} else {
    http_response_code(400);
    echo custom_array($sub->status);
}