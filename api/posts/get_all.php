<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_post.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';
include '../../models/IdTypes.php';

try {
    if (get_isset('userId')) {
        $post->user_id = set_get_variable('userId');
    } else {
        $post->User_id = null;
    }
    
    if (get_isset('isEdited')) {
        $post->is_edited = set_get_variable('isEdited');
    } else {
        $post->is_edited = null;
    }
    
    if (get_isset('datePosted')) {
        $post->date_posted = set_get_variable('datePosted');
    } else {
        $post->date_posted = null;
    }

    $post->search = get_isset('search') ? set_get_variable('search') : null;
    $post->index = get_isset('index') ? set_get_variable('index') : null;
    
    $post->validate_id(IdTypes::UserId);
    $post->validate_id(IdTypes::Index, true);
    $post->validate_boolean(BooleanTypes::IsEdited, true);
    
    if ($post->status_is_empty()) {
        if (!$decoded->isAdmin) {
            if ($post->user_id === null) {
                if ($post->date_posted === null && $post->is_edited == null) {
                    http_response_code(403);
                    echo custom_array(Post::$all_params_null);
                    die();
                } else {
                    http_response_code(403);
                    echo custom_array(Post::$user_id_null);
                    die();
                }
            } else if ($decoded->userId !== $post->user_id) {
                http_response_code(403);
                echo custom_array(Post::$not_auth);
                die();
            }
        }
    
        $results = $post->get_all(null, null);
        $count = $results->rowCount();
    
        if ($count > 0) {
            $posts_array = array();
    
            while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
    
                $item_arr = array(
                    'postId' => $PostId,
                    'postBody' => $PostBody,
                    'isEdited' => boolval($IsEdited),
                    'datePosted' => $DatePosted
                );
    
                if ($decoded->userId !== $post->user_id) {
                    $item_arr['userId'] = $UserId;
                    $item_arr['firstName'] = $FirstName;
                    $item_arr['lastName'] = $LastName;
                }
    
                array_push($posts_array, $item_arr);
            }
    
            http_response_code(200);
            echo json_encode(array(
                'posts' => $posts_array
            ));
        } else {
            http_response_code(404);
            echo custom_array('No posts found');
        }
    } else {
        http_response_code(400);
        echo custom_array($post->status);
    }
} catch (Exception $e) {
    http_response_code(500);
    $post->createError($e);
    echo custom_array($post->err_message);
}