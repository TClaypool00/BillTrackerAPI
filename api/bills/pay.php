<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../partail_files/jwt_partial.php';

try {
    $bill->bill_id = set_id();

    if ($bill->bill_exists()) {
        $bill->user_id;

        if ($bill->user_has_bill()) {
            if ($bill->is_paid($bill->bill_id, 1)) {
                http_response_code(400);
                echo custom_array('Bill has already been paid');
            } else {
                if ($bill->pay($bill->bill_id, 1)) {
                    http_response_code(400);
                    echo custom_array('Bill has already been paid');
                } else {
                    http_response_code(400);
                    echo custom_array('Bill could not be paid');
                }
            }
        } else {
            http_response_code(403);
            echo custom_array($bill->not_access_bill);
        }
    } else {
        http_response_code(404);
        echo custom_array('Bill with an id of ' . $bill->bill_id . ' could not be found.');
    }
} catch(Throwable $e) {
    http_response_code(500);
    $bill->createError($e);
    echo custom_array($bill->err_message);
}