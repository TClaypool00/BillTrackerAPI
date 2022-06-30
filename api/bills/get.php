<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';

$bill->bill_id = set_id();
$bill->user_id = $decoded->userId;

if (!$bill->bill_exists()) {
    http_response_code(404);
    echo custom_array('Bill with an id of ' . $bill->bill_id . ' was not found');
    die();
}


if (get_isset('showCurrency')) {
    $bill->show_currency = set_get_variable('showCurrency');
} else {
    $bill->show_currency = false;
}

if (!$decoded->isAdmin && !$bill->user_has_bill()) {
    http_response_code(403);
    echo custom_array($bill->not_access_bill);
    die();
}

$bill->validate_boolean(BooleanTypes::ShowCurrency, true);

if ($bill->status_is_empty()) {
    $bill->get();

    if ($bill->show_currency) {
        $bill->amount_due = currency($bill->amount_due);
    }

    $bill_arr = array(
        'billId' => $bill->bill_id,
        'billName' => $bill->bill_name,
        'amountDue' => $bill->amount_due,
        'isActive' => boolval($bill->is_active),
        'dateDue' => $bill->date_due,
        'datePaid' => $bill->date_paid,
        'isPaid' => boolval($bill->is_paid),
        'isLate' => boolval($bill->is_late),
        'companyId' => $bill->company_id,
        'companyName' => $bill->company_name,
        'userId' => $bill->user_id,
        'firstName' => $bill->user_first_name,
        'lastName' => $bill->user_last_name
    );

    http_response_code(200);
    print_r(json_encode($bill_arr));
} else {
    http_response_code(400);
    echo custom_array($bill->status);
}