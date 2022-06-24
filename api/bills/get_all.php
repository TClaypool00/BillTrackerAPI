<?php
include '../../partail_files/get_all_header.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../global_functions.php';

$by_user = false;
$by_active = false;

if (get_isset('userId')) {
    $bill->user_id = set_get_variable('userId');
    $by_user = true;
}

if (get_isset('isActive')) {
    $bill->is_active = set_get_variable('isActive');
    $by_active = true;
}

$result = $bill->get_all($by_user, $by_active);

$num = $result->rowCount();

if ($num > 0) {
    $bill_arr = array();

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $bill_item = array(
            'billId' => $BillId,
            'billName' => $BillName,
            'amountDue' => $bill->currency($AmountDue),
            'isRecurring' => $IsRecurring,
            'isActive' => $IsActive,
            'endDate' => $EndDate,
            'userId' => $UserId,
            'firstName' => $FirstName,
            'lastName' => $LastName
        );
        array_push($bill_arr, $bill_item);
    }

    http_response_code(200);
    echo json_encode($bill_arr);
} else {
    http_response_code(404);
    echo custom_array('No bills found');
}
