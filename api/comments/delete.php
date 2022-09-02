<?php
include '../../partail_files/delete_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_comment.php';
include '../../partail_files/jwt_partial.php';

try {
    $comment->comment_id = set_id();
    $comment->user_id = $decoded->userId;

    if ($comment->comment_has_access($decoded)) {
        if ($comment->delete()) {
            http_response_code(200);
            echo custom_array('Comment has been deleted');
        } else {
            http_response_code(400);
            echo custom_array('Comment could not be deleted');
        }
    } else {
        http_response_code(400);
        echo custom_array($comment->no_access);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $comment->createError($e);
    echo custom_array($comment->err_message);
}