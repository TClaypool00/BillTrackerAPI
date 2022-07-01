<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../partail_files/jwt_partial.php';

$sub->subscription_id = set_id();

if (!$sub->sub_exists()) {
    http_response_code(404);
    echo custom_array('No subscription found');
    die();
}

$sub->user_id = $decoded->userId;

if (!$sub->user_has_sub()) {
    http_response_code(403);
    echo custom_array(Subscription::$not_access);
    die();
}

if (get_isset('showCurrency')) {
    $sub->show_currency = set_get_variable('showCurrency');
} else {
    $sub->show_currency = false;
}

if ($sub->status_is_empty()) {
    $sub->get();

    if ($sub->show_currency) {
        $sub->amount_due = currency($sub->amount_due);
    }

    $sub_arr = array(
        'subscriptionId' => $sub->subscription_id,
        'name' => $sub->name,
        'amountDue' => $sub->amount_due,
        'dueDate' => $sub->due_date,
        'isActive' => boolval($sub->is_active),
        'dateDue' => $sub->date_due,
        'datePaid' => $sub->date_paid,
        'isPaid' => boolval($sub->is_paid),
        'isLate' => boolval($sub->is_late),
        'companyId' => $sub->company_id,
        'companyName' => $sub->company_name,
        'userId' => $sub->user_id,
        'firstName' => $sub->user_first_name,
        'lastName' => $sub->user_last_name
    );

    http_response_code(200);
    print_r(json_encode($sub_arr));
} else {
    http_response_code(400);
    echo custom_array($sub->status);
}