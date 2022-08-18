<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_suggestion.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';

try {
    $suggestion->suggestion_id = set_id();
    $suggestion->suggestion_header = $data->suggestionHeader ?? null;
    $suggestion->suggestion_body = $data->suggestionBody ?? null;
    $suggestion->user_id = $decoded->userId;
    define('MESSAGE', 'Suggestion has been updated');

    if (!$suggestion->suggestion_exists()) {
        http_response_code(404);
        echo custom_array($suggestion->not_found());
        die();
    }

    if (get_isset('returnObject')) {
        $suggestion->return_object = set_get_variable('returnObject');
    } else {
        $suggestion->return_object = true;
    }
    
    $suggestion->validate_boolean(BooleanTypes::ReturnObject);
    $suggestion->format_data();
    $suggestion->data_is_empty();
    $suggestion->data_too_long();

    if ($suggestion->status_is_empty()) {
        if ($decoded->isAdmin || $suggestion->user_has_sugguestion()) {
            if ($suggestion->suggestion_name_exists(true)) {
                http_response_code(400);
                echo custom_array('Suggestion already exists');
            } else {
                if ($suggestion->update()) {
                    http_response_code(200);
                    if ($suggestion->return_object) {
                        $suggestion->get();
                        echo $suggestion->suggestion_array(MESSAGE, true, true);
                    } else {
                        echo custom_array(MESSAGE);
                    }
                } else {
                    http_response_code(400);
                    echo custom_array('Suggeston could not be updated');
                }
            }
        } else {
            http_response_code(403);
            echo custom_array(Suggestion::$not_auth);
        }
    } else {
        http_response_code(400);
        echo custom_array($suggestion->status);
    }
} catch (Exception $e) {
    http_response_code(500);
    $suggestion->createError($e);
    echo custom_array($suggestion->err_message);
}