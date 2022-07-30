<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_post.php';
include '../../partail_files/jwt_partial.php';

$post->post_body = $data->post_body ?? null;

$post->data_is_null();
$post->validate_data();

try{
    if ($post->status_is_empty()) {
        $post->user_id = $decoded->userId;
    
        if ($post->create()) {
            http_response_code(201);
            echo  custom_array('Post has been added');
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
    echo custom_array($e->getMessage());
}