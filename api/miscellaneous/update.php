<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_miscellaneous.php';
include '../../partail_files/jwt_partial.php';

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

    $misc->name = $data->name ?? null;
    $misc->amount = $data->amount ?? null;
    $misc->company_id = $data->companyId ?? null;

    $misc->data_is_null();
    $misc->validate_data();
    $misc->validate_company_id();

    if ($misc->status_is_empty()) {
        if (!$misc->user_has_company()) {
            http_response_code(403);
            echo custom_array(Miscellaneous::$does_not_have_company);
            die();
        }

        if($misc->update()) {
            http_response_code(200);
            print_r($misc->miscellaneous_array(true, $misc->user_id !== $decoded->userId, 'Miscellaneous has been updated', false));
        } else {
            http_response_code(400);
            echo custom_array('Miscellaneous could not be updated');
        }
    } else {
        http_response_code(400);
        echo custom_array($misc->status);
    }
} catch (Exception $e) {
    http_response_code(500);
    $misc->createError($e);
    echo custom_array($misc->err_message);
}