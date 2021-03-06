<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../partail_files/jwt_partial.php';

$loan->loan_id = set_id();

if (!$loan->loan_exists()) {
    http_response_code(404);
    echo custom_array($loan->loan_not_found);
    die();
}

$loan->loan_name = $data->loanName ?? null;
$loan->is_active = $data->isActive ?? null;
$loan->monthly_amt_due = $data->monthlyAmtDue ?? null;
$loan->total_loan_amt = $data->totalAmtDue ?? null;
$loan->remaining_amt = $data->remaingAm ?? null;
$loan->company_id = $data->companyId ?? null;

$loan->data_is_null();
$loan->format_data();
$loan->validate_company_id();
$loan->validate_is_active();

if ($loan->status === '') {
    $loan->user_id = $decoded->userId;

    if (!$loan->user_has_company()) {
        http_response_code(403);
        echo custom_array(Loan::$does_not_have_company);
        die();
    }

    if ($loan->update()) {
        http_response_code(200);
        echo custom_array('Loan has been updated');
    } else {
        http_response_code(400);
        echo custom_array('Loan could not updated');
    }
} else {
    http_response_code(400);
    echo custom_array($loan->status);
}