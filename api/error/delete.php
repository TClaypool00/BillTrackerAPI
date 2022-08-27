<?php
include '../../partail_files/delete_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_error.php';
include '../../partail_files/jwt_partial.php';

try {
    $error->user_id = $decoded->userId;
    if ($decoded->isAdmin) {
        if ($error->error_exists()) {
            if ($error->delete()) {
                http_response_code(200);
                echo custom_array('Error has been deleted');
            } else {
                http_response_code(400);
                echo custom_array('Error could not be deleted');
            }
        } else {
            http_response_code(404);
            echo custom_array($error->error_not_found);
        }
    } else {
        http_response_code(403);
        echo custom_array(TrackerError::$not_auth);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $error->createError($e);
    echo custom_array($error->err_message);
}