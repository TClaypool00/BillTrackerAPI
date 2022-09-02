<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_miscellaneous.php';
include '../../partail_files/jwt_partial.php';
include '../../models/IdTypes.php';

try {
    define('MESSAGE', 'Miscellaneous has been added');
    $misc->name = $data->name ?? null;
    $misc->amount = $data->amount ?? null;
    $misc->company_id = $data->companyId ?? null;
    $misc->user_id = $decoded->userId;
    $misc->date_added = $data->dateAdded ?? date('Y-m-d');

    $misc->data_is_null();
    $misc->validate_data();
    $misc->validate_id(IdTypes::CompanyId);
    $misc->data_is_too_long();

    if ($misc->status_is_empty()) {
        if (!$misc->user_has_company()) {
            http_response_code(403);
            echo custom_array(Miscellaneous::$does_not_have_company);
            die();
        }

        $misc->create();

        if (is_numeric($misc->miscellaneous_id) && $misc->miscellaneous_id !== 0) {
            http_response_code(201);
            if ($misc->return_object) {
                $misc->get();
                print_r($misc->miscellaneous_array(false, $misc->user_id !== $decoded->userId, MESSAGE, $misc->show_currency));
            } else {
                echo custom_array(MESSAGE);
            }
        } else {
            http_response_code(400);
            echo custom_array('Miscellaneous could not be added');
        }
    } else {
        http_response_code(400);
        echo custom_array($misc->status);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $misc->createError($e);
    echo custom_array($misc->err_message);
}