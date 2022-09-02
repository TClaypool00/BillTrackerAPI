<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../partail_files/jwt_partial.php';

try {
    $loan->loan_id = set_id();

    if ($loan->loan_exists()) {
        $loan->user_id = $decoded->userId;

        if ($loan->user_has_loan()) {
            if ($loan->is_paid($loan->loan_id, 2)) {
                http_response_code(200);
                echo custom_array('Loan has already been paid');
            } else {
                if ($loan->pay($loan->loan_id, 2)) {
                    http_response_code(200);
                    echo custom_array('Loan has been paid');
                } else {
                    http_response_code(400);
                    echo custom_array('Loan could not be paid');
                }
            }
        } else {
            http_response_code(403);
            echo custom_array(Loan::$not_access_to_loan);
        }
    } else {
        http_response_code(404);
        echo custom_array('Loan with an id of ' . $loan->loan_id . ' does exists') ;
    }
} catch (Throwable $e) {
    http_response_code(500);
    $loan->createError($e);
    echo custom_array($loan->err_message);
}