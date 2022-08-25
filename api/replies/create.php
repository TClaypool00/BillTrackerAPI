<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_reply.php';
include '../../partail_files/jwt_partial.php';
include '../../models/IdTypes.php'; 

try {
    $reply->reply_body = $data->replyBody ?? null;
    $reply->comment_id = $data->commentId ?? null;
    $reply->user_id = $decoded->userId;

    $reply->validate_body();
    $reply->validate_id(IdTypes::CommentId);

    if ($reply->status_is_empty()) {
        if ($reply->comment_has_access()) {
            $reply->create();
    
            if (is_numeric($reply->reply_id) && $reply->reply_id !== 0) {
                http_response_code(201);
                $reply->get();
                echo $reply->reply_array(true, 'Reply has been added');
            } else {
                http_response_code(400);
                echo custom_array('Reply could not be added');
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