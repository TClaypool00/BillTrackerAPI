<?php
include '../../partail_files/delete_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_reply.php';
include '../../partail_files/jwt_partial.php';

try {
    $reply->reply_id = set_id();
    $reply->user_id = $decoded->userId;

    if ($reply->has_access_reply($decoded)) {
        if ($reply->delete()) {
            http_response_code(200);
            echo custom_array('Reply has been deleted');
        } else {
            http_response_code(400);
            echo custom_array('Reply could not be deleted');
        }
    } else {
        http_response_code(400);
        echo custom_array($reply->no_access);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $reply->createError($e);
    echo custom_array($reply->err_message);
}