<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_suggestion.php';
include '../../partail_files/jwt_partial.php';

try {
    $suggestion->suggestion_header = $data->suggestionHeader ?? null;
    $suggestion->suggestion_body = $data->suggestionBody ?? null;
    $suggestion->user_id = $decoded->userId;

    $suggestion->format_data();
    $suggestion->data_is_empty();
    $suggestion->data_too_long();

    if ($suggestion->status_is_empty()) {
        if ($suggestion->suggestion_name_exists()) {
            http_response_code(400);
            echo custom_array("A suggestion with header '" . $suggestion->suggestion_header . "'");
            die();
        } else {
            $suggestion->create();
            if (is_numeric($suggestion->suggestion_id)) {
                http_response_code(201);
                echo $suggestion->suggestion_array('Suggestion has been created', true);
            } else {
                http_response_code(400);
                echo custom_array('Suggestion could not be created');
            }
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