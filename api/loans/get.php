<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';

$loan->loan_id = set_id();

if (!$loan->loan_exists()) {
    http_response_code(404);
    echo custom_array($loan->loan_not_found);
    die();
}

$loan->user_id = $decoded->userId;

if (!$decoded->isAdmin && !$loan->user_has_loan()) {
    http_response_code(403);
    echo custom_array(Loan::$not_access_to_loan);
    die();
}

if (get_isset('showCurrency')) {
    $loan->show_currency = set_get_variable('showCurrency');
} else {
    $loan->show_currency = false;
}

if (get_isset('isEdit')) {
    $loan->is_edit = set_get_variable('isEdit');
} else {
    $loan->is_edit = false;
}

$loan->validate_boolean(BooleanTypes::ShowCurrency);
$loan->validate_boolean(BooleanTypes::IsEdit);

if ($loan->status_is_empty()) {
    if ($loan->show_currency && $loan->is_edit) {
        http_response_code(400);
        echo custom_array(Loan::$is_edit_show_currency);
        die();
    }

    $loan->get();

    if ($loan->show_currency) {
        $loan->monthly_amt_due = currency($loan->monthly_amt_due);
        $loan->total_loan_amt = currency($loan->total_loan_amt);
        $loan->remaining_amt = currency($loan->remaining_amt);
    }

    $loan_arr = array(
        'loanId' => $loan->loan_id,
        'loanName' => $loan->loan_name,
        'isActive' => boolval($loan->is_active),
        'monthlyAmountDue' => $loan->monthly_amt_due,
        'totalAmountDue' => $loan->total_loan_amt,
        'remainingAmount' => $loan->remaining_amt,
        'dateDue' => $loan->date_due,
        'datePaid' => $loan->date_paid,
        'isPaid' => boolval($loan->is_paid),
        'isLate' => boolval($loan->is_late),
        'companyId' => $loan->company_id
    );

    if ($loan->is_edit) {
        $loan_arr['companies'] = $loan->drop_down();
    } else {
        $loan_arr['companyName'] = $loan->company_name;
    }

    if ($decoded->userId !== $loan->user_id) {
        $loan_arr['userId'] = $loan->user_id;
        $loan_arr['firstName'] = $loan->user_first_name;
        $loan_arr['lastName'] = $loan->user_last_name;
    }

    http_response_code(200);
    print_r(json_encode($loan_arr));
} else {
    http_response_code(400);
    echo custom_array($loan->status);
}