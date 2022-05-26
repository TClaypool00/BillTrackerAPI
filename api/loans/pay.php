<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../global_functions.php';

$loan->loan_id = set_id();
$amount = $data->$amount;

if ($loan->pay($amount, $loan->loan_id, 2)) {
    http_response_code(200);
    echo custom_array('Loan has been paid');
} else {
    http_response_code(400);
    echo custom_array('Loan has been paid');
}