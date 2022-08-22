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

    if (get_isset('includeDropDown')) {
        $misc->include_drop_down = set_get_variable('includeDropDown');
    } else {
        $misc->include_drop_down = true;
    }

    $misc->validate_boolean(BooleanTypes::ShowCurrency);
    $misc->validate_boolean(BooleanTypes::IncludeDropDown);

    if ($misc->status_is_empty()) {
        $misc->get();        

        http_response_code(200);
        print_r($misc->miscellaneous_array($misc->include_drop_down, $misc->user_id !== $decoded->userId, null, $misc->show_currency));
    } else {
        http_response_code(400);
        echo custom_array($misc->status);
    }
} catch (Exception $e) {
    http_response_code(500);
    $misc->createError($e);
    echo custom_array($misc->err_message);
}