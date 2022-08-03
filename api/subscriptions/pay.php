<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../partail_files/jwt_partial.php';

try {
    $sub->subscription_id = set_id();

    if ($sub->sub_exists()) {
        $sub->user_id = $decoded->userId;

        if (!$sub->user_has_sub()) {
            if ($sub->is_paid($sub->subscription_id, 3)) {
                http_response_code(200);
                echo custom_array('Subscription has already been paid');
            } else {
                if ($sub->pay($sub->subscription_id, 3)) {
                    http_response_code(200);
                    echo custom_array('Subscription has already been paid');
                } else {
                    http_response_code(400);
                    echo custom_array('Subscription could not be paid');
                }
            }
        } else {
            http_response_code(403);
            echo custom_array(Subscription::$not_access);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo custom_array($e->getMessage());
}