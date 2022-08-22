<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';

try {
    $loan->loan_id = set_id();

    if (!$loan->loan_exists()) {
        http_response_code(404);
        echo custom_array($loan->loan_not_found);
        die();
    }

    $loan->user_id = $decoded->userId;

    if (!$decoded->isAdmin && !$loan->user_has_loan()) {
        http_response_code(403);
        echo custom_array(Loan::$not_access_to_loan);
        die();
    }

    if (get_isset('showCurrency')) {
        $loan->show_currency = set_get_variable('showCurrency');
    } else {
        $loan->show_currency = false;
    }

    if (get_isset('includeDropDown')) {
        $loan->include_drop_down = set_get_variable('includeDropDown');
    } else {
        $loan->include_drop_down = true;
    }

    $loan->validate_boolean(BooleanTypes::ShowCurrency);
    $loan->validate_boolean(BooleanTypes::IncludeDropDown);

    if ($loan->status_is_empty()) {
        $loan->get();

        http_response_code(200);
        print_r(json_encode($loan->loan_array($loan->include_drop_down, $loan->user_id !== $decoded->userId, null, $loan->show_currency)));
    } else {
        http_response_code(400);
        echo custom_array($loan->status);
    }
} catch (Exception $e) {
    http_response_code(500);
    $loan->createError($e);
    echo custom_array($loan->err_message);
}