<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../partail_files/jwt_partial.php';

try {
    $bill->bill_name = $data->billName ?? null;
    $bill->amount_due = $data->amountDue ?? null;
    $bill->company_id = $data->companyId ?? null;
    $bill->date_due = $data->dueDate ?? null;

    $bill->data_is_null();
    $bill->validate_bill_name();
    $bill->validate_amount_due();
    $bill->validate_company_id();
    $bill->validate_date();

    if ($bill->status === '') {
        $bill->user_id = $decoded->userId;

        if (!$bill->user_has_company()) {
            http_response_code(403);
            echo custom_array(Bill::$does_not_have_company);
            die();
        }

        if ($bill->create()) {
            http_response_code(201);
            echo custom_array('Bill has been created');
        } else {
            http_response_code(400);
            echo custom_array('Bill could not be created');
        }
    } else {
        http_response_code(400);
        echo custom_array($bill->status);
    }
} catch (Exception $e) {
    echo custom_array($e->getMessage());
}