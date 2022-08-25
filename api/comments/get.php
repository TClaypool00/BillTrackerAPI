<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_comment.php';
include '../../partail_files/jwt_partial.php';

try {
    $comment->comment_id = set_id();
    $comment->user_id = $decoded->userId;

    if ($comment->has_access($decoded)) {
        $comment->get();

        http_response_code(200);
        echo $comment->comment_array($comment->user_id !== $decoded->userId);
    } else {
        http_response_code(400);
        echo custom_array($comment->no_access);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $comment->createError($e);
    echo 'Error: ' .  $e->getTraceAsString();
    echo custom_array($comment->err_message);
}