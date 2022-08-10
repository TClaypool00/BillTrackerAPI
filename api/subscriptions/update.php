<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../partail_files/jwt_partial.php';

try {
    $sub->subscription_id = set_id();

    if (!$sub->sub_exists()) {
        http_response_code(404);
        echo custom_array('Subscription does exist');
        die();
    }

    $sub->name = $data->name ?? null;
    $sub->amount_due = $data->amountDue ?? null;
    $sub->date_due = $data->dueDate ?? null;
    $sub->is_active = $data->isActive ?? null;
    $sub->company_id = $data->companyId ?? null;

    $sub->data_is_null();
    $sub->validate_data();
    $sub->data_too_small();
    $sub->data_too_long();
    $sub->validate_company_id();
    $sub->validate_date();
    $sub->validate_is_active();

    if ($sub->status_is_empty()) {
        $sub->user_id = $decoded->userId;

        if (!$sub->user_has_sub()) {
            http_response_code(403);
            echo custom_array(Subscription::$not_access);
            die();
        }

        if (!$sub->user_has_company()) {
            http_response_code(403);
            echo custom_array(Subscription::$does_not_have_company);
            die();
        }

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
} catch (Exception $e) {
    http_response_code(500);
    $sub->createError($e);
    echo custom_array($sub->err_message);
}