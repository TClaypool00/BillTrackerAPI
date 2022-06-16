<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../partail_files/jwt_partial.php';

$loan->loan_id = set_id();

if (!$loan->loan_exists()) {
    http_response_code(404);
    echo custom_array($loan->loan_not_found);
    die();
}

$loan->get();

$loan_arr = array(
    'loanId' => $loan->loan_id,
    'loanName' => $loan->loan_name,
    'isActive' => $loan->is_active,
    'monthlyAmountDue' => $loan->monthly_amt_due,
    'totalAmountDue' => $loan->total_loan_amt,
    'remainingAmount' => $loan->remaining_amt,
    'companyId' => $loan->company_id,
    'companyName' => $loan->company_name,
    'userId' => $loan->user_id,
    'firstName' => $loan->user_first_name,
    'lastName' => $loan->user_last_name
);

http_response_code(200);
print_r(json_encode($loan_arr));