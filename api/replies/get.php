<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_reply.php';
include '../../partail_files/jwt_partial.php';

try {
    $reply->reply_id = set_id();
    $reply->user_id = $decoded->userId;

    if ($reply->has_access_reply($decoded)) {
        $reply->get();

        http_response_code(200);
        $not_same_id = $decoded->userId !== $reply->user_id;
        echo $reply->reply_array($not_same_id, null, $not_same_id);
    } else {
        http_response_code(400);
        echo custom_array($reply->err_message);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $reply->createError($e);
    echo custom_array($reply->err_message);
}