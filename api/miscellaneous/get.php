<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_miscellaneous.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';

try {
    $misc->miscellaneous_id = set_id();

    if (!$misc->miscellaneous_exists()) {
        http_response_code(404);
        echo custom_array('Miscellaneous does not exists');
        die();
    }

    $misc->user_id = $decoded->userId;

    if (!$misc->user_has_miscellaneous()) {
        http_response_code(403);
        echo custom_array(Miscellaneous::$not_has_access);
        die();
    }

    if (get_isset('showCurrency')) {
        $misc->show_currency = set_get_variable('showCurrency');
    } else {
        $misc->show_currency = false;
    }

    $misc->validate_boolean(BooleanTypes::ShowCurrency);

    if ($misc->status_is_empty()) {
        $misc->get();

        if ($misc->show_currency) {
            $misc->amount = currency($misc->amount);
        }

        $misc_arr = array(
            'miscellaneousId' => $misc->miscellaneous_id,
            'name' => $misc->name,
            'amount' => $misc->amount,
            'companyId' => $misc->company_id,
            'companyName' => $misc->company_name,
            'userId' => $misc->user_id,
            'firstName' => $misc->user_first_name,
            'lastName' => $misc->user_last_name
        );

        http_response_code(200);
        print_r(json_encode($misc_arr));
    } else {
        http_response_code(400);
        echo custom_array($misc->status);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo custom_array($e->getMessage());
}