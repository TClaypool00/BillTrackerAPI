<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_comment.php';
include '../../partail_files/jwt_partial.php';

try {
    $comment->comment_id = set_id();
    $comment->comment_body = $data->commentBody ?? null;
    $comment->user_id = $decoded->userId;

    $comment->validate_body();

    if ($comment->status_is_empty()) {
        if ($comment->has_access($decoded)) {
            if (!$comment->update()) {
                http_response_code(400);
                echo custom_array('Comment could not be updated');
            } else {
                http_response_code(200);
                $comment->get();
                echo $comment->comment_array(true, 'Comment updated');
            }
        } else {
            http_response_code(400);
            echo custom_array($comment->no_access);
        }
    } else {
        http_response_code(400);
        echo custom_array($comment->status);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $comment->createError($e);
    echo custom_array($comment->err_message);
}