<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../global_functions.php';

$loan->loan_id = set_id();
$loan->loan_name = $data->loanName;
$loan->is_active = $data->isActive;
$loan->monthly_amt_due = $data->monthlyAmtDue;
$loan->total_loan_amt = $data->totalAmtDue;
$loan->remaining_amt = $data->remaingAmt;

if ($loan->update()) {
    http_response_code(200);
    echo custom_array('Loan has been updated');
} else {
    http_response_code(400);
    echo custom_array('Loan could not updated');
}