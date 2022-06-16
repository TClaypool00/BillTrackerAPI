<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../partail_files/jwt_partial.php';

$loan->loan_id = set_id();
$amount = $data->amount;

if ($loan->loan_exists()) {
    if ($loan->is_paid($loan->loan_id)) {
        http_response_code(200);
        echo custom_array('Loan has already been paid');
    } else {
        if ($loan->pay($amount, $loan->loan_id, 2)) {
            http_response_code(200);
            echo custom_array('Loan has been paid');
        } else {
            http_response_code(400);
            echo custom_array('Loan could not be paid');
        }
    }
} else {
    http_response_code(404);
    echo custom_array('Loan with an id of ' . $loan->loan_id . ' does exists') ;
}