<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_error.php';
include '../../partail_files/jwt_partial.php';

try {
    $error->error_id = set_id();
    $error->user_id = $decoded->userId;

    if ($decoded->isAdmin) {
        if ($error->error_exists()) {
            $error->get();

            $err_arr = array(
                'errorId' => $error->error_id,
                'errorMessage' => $error->err_message,
                'errorCode' => $error->code,
                'errorLine' => $error->line,
                'stackTrace' => $error->stack_trace,
                'usersCount' => $error->num_users
            );

            http_response_code(200);
            print_r(json_encode($err_arr));
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