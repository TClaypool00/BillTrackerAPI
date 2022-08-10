<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_post.php';
include '../../partail_files/jwt_partial.php';

try{
    $post->post_body = $data->postBody ?? null;

    $post->data_is_null();
    $post->validate_data();

    if ($post->status_is_empty()) {
        $post->user_id = $decoded->userId;
        $post->create();
        if ($post->post_id !== null) {
            http_response_code(201);
            echo $post->post_array('Post has been created!');
        } else {
            http_response_code(400);
            echo custom_array('Post could not be added');
        }
    } else {
        http_response_code(400);
        echo custom_array($post->status);
    }
} catch(Exception $e) {
    http_response_code(500);
    $post->createError($e);
    echo custom_array($post->err_message);
}