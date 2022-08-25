<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_comment.php';
include '../../partail_files/jwt_partial.php';
include '../../models/IdTypes.php';

try {
    $comment->comment_body = $data->commentBody ?? null;
    $comment->type_id = $data->typeId ?? null;
    $comment->parent_id = $data->parentId ?? null;
    $comment->user_id = $decoded->userId;

    $comment->validate_body();
    $comment->validate_id(IdTypes::TypeId);
    $comment->validate_id(IdTypes::ParentId);

    if ($comment->status_is_empty()) {
        $comment->create();

        if (is_numeric($comment->comment_id) && $comment->comment_id !== 0) {
            http_response_code(201);
            $comment->get();
            echo $comment->comment_array(true, 'Comment has been created');
        } else {
            http_response_code(400);
            echo custom_array('Comment could not be created');
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

