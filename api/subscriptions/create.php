<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../partail_files/jwt_partial.php';

try {
    $sub->name = $data->name ?? null;
    $sub->amount_due = $data->amountDue ?? null;
    $sub->date_due = $data->dueDate ?? null;
    $sub->company_id = $data->companyId ?? null;

    $sub->data_is_null();
    $sub->validate_data();
    $sub->data_too_small();
    $sub->data_too_long();
    $sub->validate_company_id();
    $sub->validate_date();

    if ($sub->status_is_empty()) {
        $sub->user_id = $decoded->userId;

        if (!$sub->user_has_company()) {
            http_response_code(403);
            echo custom_array(Subscription::$does_not_have_company);
            die();
        }

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
} catch (Exception $e) {
    http_response_code(500);
    echo custom_array($e->getMessage());
}