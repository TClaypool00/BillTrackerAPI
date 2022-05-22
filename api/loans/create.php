<?php
include '../../partail_files/create_header.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../global_functions.php';

$loan->loan_name = $data->loanName;
$loan->monthly_amt_due = $data->monthlyAmtDue;
$loan->total_loan_amt = $data->totalAmtDue;
$loan->remaining_amt = $data->remaingAmt;
$loan->company_id = $data->companyId;
$loan->date_due = $data->dateDue;

if ($loan->create()) {
    http_response_code(201);
    echo custom_array('Loan has been created');
} else {
    http_response_code(400);
    echo custom_array('Loan could not be created');
}