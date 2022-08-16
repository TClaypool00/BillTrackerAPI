<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_suggestion.php';
include '../../partail_files/jwt_partial.php';

try {
    $suggestion->suggestion_id = set_id();

    if (!$suggestion->suggestion_exists()) {
        http_response_code(404);
        echo custom_array($suggestion->not_found());
        die();
    }

    if (!$decoded->isAdmin && !$suggestion->user_has_sugguestion()) {
        http_response_code(403);
        echo custom_array(Suggestion::$not_auth);
        die();
    }

    $suggestion->get();

    echo $suggestion->suggestion_array('', $decoded->userId !== $suggestion->user_id);
} catch (Exception $e) {
    http_response_code(500);
    $suggestion->createError($e);
    echo custom_array($suggestion->err_message);
}