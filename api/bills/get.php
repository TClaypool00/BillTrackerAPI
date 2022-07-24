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
$bill->validate_boolean(BooleanTypes::IsEdit, true);

if ($bill->status_is_empty()) {
    if ($bill->show_currency && $bill->is_edit) {
        http_response_code(400);
        echo custom_array(Loan::$is_edit_show_currency);
        die();
    }

    $bill->get();

    $bill_arr = array(
        'billId' => $bill->bill_id,
        'billName' => $bill->bill_name,
        'isActive' => boolval($bill->is_active),
        'amountDue' => $bill->amount_due,
        'dateDue' => $bill->date_due,
        'datePaid' => $bill->date_paid,
        'isPaid' => boolval($bill->is_paid),
        'isLate' => boolval($bill->is_late),
    );

    $bill_arr['companyId'] = $bill->company_id;
    $bill_arr['company'] = $bill->drop_down();
    $bill_arr['companyName'] = $bill->company_name;

    if ($bill->user_id !== $decoded->userId) {
        $bill_arr['userId'] = $bill->user_id;
        $bill_arr['firstName'] = $bill->user_first_name;
        $bill_arr['lastName'] = $bill->user_last_name;
    }

    http_response_code(200);
    print_r(json_encode($bill_arr));
} else {
    http_response_code(400);
    echo custom_array($bill->status);
}