<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../partail_files/jwt_partial.php';

$bill->bill_id = set_id();
$bill->user_id = $decoded->userId;

if (!$decoded->isAdmin && !$bill->user_has_bill()) {
    http_response_code(403);
    echo custom_array($bill->not_access_bill);
    die();
}

$bill->get();

if ($bill->bill_name != null) {
    $bill_arr = array(
        'billId' => $bill->bill_id,
        'billName' => $bill->bill_name,
        'amountDue' => $bill->amount_due_curr,
        'isRecurring' => $bill->is_recurring,
        'isActive' => $bill->is_active,
        'endDate' => $bill->end_date,
        'userId' => $bill->user_id,
        'firstName' => $bill->user_first_name,
        'lastName' => $bill->user_last_name
    );

    http_response_code(200);
    print_r(json_encode($bill_arr));
} else {
    http_response_code(404);
    echo custom_array('no bill found');
}