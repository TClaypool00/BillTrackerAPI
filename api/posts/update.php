<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_post.php';
include '../../partail_files/jwt_partial.php';

try{
    $post->post_id = set_id();
    $post->post_body = $data->postBody ?? null;

    if ($post->post_exists()) {
        $post->user_id = $decoded->userId;
        if (!$decoded->isAdmin && !$post->user_has_post()) {
            http_response_code(403);
            echo custom_array(Post::$not_auth);
            die();
        }

        $post->data_is_null();
        $post->validate_data();

        if ($post->status_is_empty()) {
            $post->update();
            if ($post->date_posted !== null) {
                http_response_code(200);
                echo $post->post_array('Post has been updated');
            } else {
                http_response_code(400);
                echo custom_array('Post could not be updated');
            }
        } else {
            http_response_code(400);
            echo custom_array($post->status);
        }
    } else {
        http_response_code(404);
        echo custom_array(Post::$post_does_not_exist);
    }
} catch(Exception $e) {
    http_response_code(500);
    echo custom_array($e->getMessage());
}