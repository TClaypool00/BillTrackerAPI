<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';
include '../../models/IdTypes.php';

define('MESSAGE', 'Bill has been updated');
$bill->bill_id = set_id();
$bill->bill_name = $data->billName ?? null;
$bill->amount_due = $data->amountDue ?? null;
$bill->is_active = $data->isActive ?? null;
$bill->user_id = $decoded->userId;
$bill->company_id = $data->companyId ?? null;

try {
    if (!$bill->bill_exists()) {
        http_response_code(404);
        echo custom_array($bill->bill_not_exists);
        die();
    }

    if (!$decoded->isAdmin) {
        if (!$bill->user_has_bill()) {
            http_response_code(403);
            echo custom_array(Bill::$not_auth);
            die();
        }

        if (!$bill->user_has_company()) {
            http_response_code(403);
            echo custom_array(Bill::$does_not_have_company);
            die();
        }
    }

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
    $bill->validate_boolean(BooleanTypes::IsActive);
    $bill->validate_boolean(BooleanTypes::IncludeDropDown);
    $bill->validate_boolean(BooleanTypes::ReturnObject);

    if ($bill->status_is_empty()) {
        $bill->update();

        if ($bill->bill_id_valid()) {
            http_response_code(200);
            if ($bill->return_object) {
                echo $bill->bill_array(MESSAGE, $decoded->userId === $bill->user_id);
            } else {
                echo custom_array(MESSAGE);
            }
        } else {
            http_response_code(400);
            echo custom_array('bill could not be updated');
        }
    } else {
        http_response_code(403);
        echo custom_array($bill->status);
    }
} catch(Throwable $e) {
    http_response_code(500);
    $bill->createError($e);
    echo custom_array($bill->err_message);
}