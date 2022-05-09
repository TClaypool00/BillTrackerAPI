<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../global_functions.php';

$sub->subscription_id = set_id();
$sub->name = $data->name;
$sub->amount_due = $data->amountDue;
$sub->due_date = $data->dueDate;
$sub->is_active = $data->isActive;
$sub->company_id = $data->companyId;

if ($sub->update()) {
    http_response_code(200);
    echo custom_array('Subscription updated');
} else {
    http_response_code(400);
    echo custom_array('Subscription could not be updated');
}