<?php
include '../../partail_files/get_all_header.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../global_functions.php';

if (get_isset('userId')) {
    $sub->user_id = set_get_variable('userId');
} else {
    $sub->user_id = null;
}

if (get_isset('companyId')) {
    $sub->company_id = set_get_variable('companyId');
} else {
    $sub->company_id = null;
}

if (get_isset('dateDue')) {
    $sub->due_date = set_get_variable('dateDue');
} else {
    $sub->due_date = null;
}

$result = $sub->get_all();
$num = $result->rowCount();

if ($num > 0) {
    $sub_arr = array();

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $sub_item = array(
            'subscriptionId' => $SubscriptionId,
            'name' => $Name,
            'amountDue' => $AmountDue,
            'dueDate' => $DateDue,
            'isActive' => $IsActive,
            'companyId' => $CompanyId,
            'companyName' => $CompanyName,
            'userId' => $UserId,
            'firstName' => $FirstName,
            'lastName' => $LastName
        );

        array_push($sub_arr, $sub_item);
    }
    
    echo json_encode($sub_arr);
} else {
    echo custom_array('No subscriptions found');
}

http_response_code(200);