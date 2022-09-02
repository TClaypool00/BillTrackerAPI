<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';

try {
    $sub->subscription_id = set_id();

    if (!$sub->sub_exists()) {
        http_response_code(404);
        echo custom_array('No subscription found');
        die();
    }

    $sub->user_id = $decoded->userId;

    if (!$sub->user_has_sub() && !$decoded->isAdmin) {
        http_response_code(403);
        echo custom_array(Subscription::$not_access);
        die();
    }

    if (get_isset('showCurrency')) {
        $sub->show_currency = set_get_variable('showCurrency');
    } else {
        $sub->show_currency = false;
    }

    if (get_isset('isEdit')) {
        $sub->is_edit = set_get_variable('isEdit');
    } else {
        $sub->is_edit = false;
    }

    $sub->validate_boolean(BooleanTypes::ShowCurrency);
    $sub->validate_boolean(BooleanTypes::IsEdit);

    if ($sub->status_is_empty()) {
        if ($sub->show_currency && $sub->is_edit) {
            http_response_code(400);
            echo custom_array(Subscription::$is_edit_show_currency);
            die();
        }

        $sub->get();

        if ($sub->show_currency) {
            $sub->amount_due = currency($sub->amount_due);
        }

        echo $sub->sub_array(true, $sub->user_id !== $decoded->userId);
    } else {
        http_response_code(400);
        echo custom_array($sub->status);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $sub->createError($e);
    echo custom_array($sub->err_message);
}