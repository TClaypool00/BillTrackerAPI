<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_post.php';
include '../../partail_files/jwt_partial.php';

try {
    $post->post_id = set_id();

    if ($post->post_exists()) {
        $post->user_id = $decoded->userId;
        if (!$decoded->isAdmin && !$post->user_has_post()) {
            http_response_code(403);
            echo custom_array(Post::$not_auth);
            die();
        }
    
        $post->get();
            
        http_response_code(200);
        echo $post->post_array('', $post->user_id === $decoded->userId);
    } else {
        http_response_code(404);
        echo custom_array(Post::$post_does_not_exist);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $post->createError($e);
    echo custom_array($post->err_message);
}