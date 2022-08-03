<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../partail_files/jwt_partial.php';

try {
    $loan->loan_name = $data->loanName ?? null;
    $loan->monthly_amt_due = $data->monthlyAmtDue ?? null;
    $loan->total_loan_amt = $data->totalAmtDue ?? null;
    $loan->remaining_amt = $data->remaingAmt ?? null;
    $loan->company_id = $data->companyId ?? null;
    $loan->date_due = $data->dateDue ?? null;

    $loan->data_is_null();
    $loan->validate_date();
    $loan->format_data();
    $loan->validate_company_id();
    $loan->validate_amount();

    if ($loan->status_is_empty()) {
        $loan->user_id = $decoded->userId;

        if (!$loan->user_has_company()) {
            http_response_code(403);
            echo custom_array(Loan::$does_not_have_company);
            die();
        }

        if ($loan->create()) {
            http_response_code(201);
            echo custom_array('Loan has been created');
        } else {
            http_response_code(400);
            echo custom_array('Loan could not be created');
        }
    } else {
        http_response_code(400);
        echo custom_array($loan->status);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo custom_array($e->getMessage());
}