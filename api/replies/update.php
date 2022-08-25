<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_reply.php';
include '../../partail_files/jwt_partial.php';

try {
    $reply->reply_id = set_id();
    $reply->reply_body = $data->replyBody ?? null;
    $reply->user_id = $decoded->userId;

    $reply->validate_body();

    if ($reply->status_is_empty()) {
        if ($reply->has_access_reply($decoded)) {
            if ($reply->update()) {
                http_response_code(200);
                $reply->get();
                echo $reply->reply_array(true, 'Reply has been updated');
            } else {
                http_response_code(400);
                echo custom_array('Reply could not be updated');
            }
        } else {
            http_response_code(400);
            echo custom_array($reply->no_access);
        }
    } else {
        http_response_code(400);
        echo custom_array($reply->status);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $reply->createError($e);
    echo custom_array($reply->err_message);
}