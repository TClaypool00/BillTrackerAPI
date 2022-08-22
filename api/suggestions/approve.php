<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_suggestion.php';
include '../../partail_files/jwt_partial.php';

try {
    $suggestion->suggestion_id = set_id();
    $suggestion->deny_reason = 'NULL';
    $suggestion->approved_denied = 1;
    $suggestion->user_id = $decoded->userId;

    if ($suggestion->suggestion_exists()) {
        if ($decoded->isAdmin) {
            if (!$suggestion->user_has_sugguestion()) {
                $suggestion->get_option_string();
                if ($suggestion->option_string_null()) {
                    if ($suggestion->approve_deny()) {
                        http_response_code(200);
                        echo custom_array('Suggestion has been approved');
                    } else {
                        http_response_code(400);
                        echo custom_array('Suggestion could not be approved');
                    }
                } else {
                    http_response_code(400);
                    echo custom_array($suggestion->option_string_message());
                }
            } else {
                http_response_code(400);
                echo custom_array('You cannot approve your own suggestion');
            }
        } else {
            http_response_code(400);
            echo custom_array('Only admins can approve suggestions');
        }
    } else {
        http_response_code(404);
        echo custom_array($suggestion->not_found());
    }
} catch (Throwable $e) {
    http_response_code(500);
    $suggestion->createError($e);
    echo custom_array($suggestion->err_message);
}