<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';
include '../../models/IdTypes.php';

try {
    if (get_isset('returnObject')) {
        $sub->return_object = set_get_variable('returnObject');
    } else {
        $sub->return_object = false;
    }

    define('MESSAGE', 'Subscription has been created');
    $sub->name = $data->name ?? null;
    $sub->amount_due = $data->amountDue ?? null;
    $sub->date_due = $data->dueDate ?? null;
    $sub->company_id = $data->companyId ?? null;
    $sub->user_id = $decoded->userId;

    $sub->data_is_null();
    $sub->validate_data();
    $sub->data_too_small();
    $sub->data_too_long();
    $sub->validate_id(IdTypes::CompanyId);
    $sub->validate_date();
    $sub->validate_boolean(BooleanTypes::ReturnObject);

    if ($sub->status_is_empty()) {
        if (!$sub->user_has_company()) {
            http_response_code(403);
            echo custom_array(Subscription::$does_not_have_company);
            die();
        }

        $sub->create();

        if (is_numeric($sub->subscription_id) && $sub->subscription_id !== 0) {
            http_response_code(201);
            if (!$sub->return_object) {
                echo custom_array(MESSAGE);
            } else {
                $sub->get();
                print_r($sub->sub_array(false, $sub->user_id !== $decoded->userId, MESSAGE));
            }
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
    $sub->createError($e);
    echo custom_array($sub->err_message);
}