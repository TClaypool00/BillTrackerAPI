<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';
include '../../models/IdTypes.php';

try {
    define('MESSAGE', 'Bill has been created');
    $bill->bill_name = $data->billName ?? null;
    $bill->amount_due = $data->amountDue ?? null;
    $bill->company_id = $data->companyId ?? null;
    $bill->date_due = $data->dueDate ?? null;
    $bill->user_id = $decoded->userId;

    if (get_isset('returnObject')) {
        $bill->return_object = set_get_variable('returnObject');
    } else {
        $bill->return_object = false;
    }

    if (get_isset('includeDropDown')) {
        $bill->include_drop_down = set_get_variable('includeDropDown');
    } else {
        $bill->include_drop_down = false;
    }

    $bill->data_is_null();
    $bill->validate_bill_name();
    $bill->validate_amount_due();
    $bill->validate_id(IdTypes::CompanyId);
    $bill->validate_date();
    $bill->validate_boolean(BooleanTypes::ReturnObject);
    $bill->validate_boolean(BooleanTypes::IncludeDropDown);

    if ($bill->status_is_empty()) {
        if (!$bill->user_has_company()) {
            http_response_code(403);
            echo custom_array(Bill::$does_not_have_company);
            die();
        }

        $bill->create();

        if ($bill->bill_id === 0 && $bill->bill_id === null) {
            http_response_code(400);
            echo custom_array('Bill could not be created');
        } else {
            http_response_code(201);
            if ($bill->return_object) {
                echo $bill->bill_array(MESSAGE, $decoded->userId === $bill->user_id);
            } else {
                echo custom_array(MESSAGE);
            }
        }
    } else {
        http_response_code(400);
        echo custom_array($bill->status);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $bill->createError($e);
    echo custom_array($bill->err_message);
}