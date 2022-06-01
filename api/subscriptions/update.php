<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../global_functions.php';

$sub->subscription_id = set_id();
$sub->name = $data->name ?? null;
$sub->amount_due = $data->amountDue ?? null;
$sub->date_due = $data->dueDate ?? null;
$sub->is_active = $data->isActive ?? null;
$sub->company_id = $data->companyId ?? null;

$sub->is_active_null();
$sub->validate_is_active();
$sub->format_data();
$sub->validate_data();

if ($sub->status === '') {
    if ($sub->update()) {
        http_response_code(200);
        echo custom_array('Subscription updated');
    } else {
        http_response_code(400);
        echo custom_array('Subscription could not be updated');
    }
} else {
    http_response_code(400);
    echo custom_array($sub->status);
}