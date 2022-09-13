<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_community.php';
include '../../partail_files/jwt_partial.php';
include '../../models/IdTypes.php';

try {
    $comm->user_id = $decoded->userId;
    $comm->index = get_isset('index') ? set_get_variable('index') : null;

    $comm->validate_id(IdTypes::Index);

    if ($comm->status_is_empty()) {
        $result = $comm->get_all();
        $num = $result->rowCount();
        $post_item = array();

        if ($num > 0) {
            $posts_arr = array();
            $comment_arr = array();
            $local_comment_id = null;
            $local_post_id = null;
            $counter = -1;
            $comment_counter = -1;
            
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                if (!in_array($PostBody, $post_item)) {

                    $post_item = array(
                        'postId' => $PostId,
                        'postBody' => $PostBody,
                        'isEdited' => boolval($PostIsEdited),
                        'datePosted' => $comm->format_date_to_string($PostDatePosted),
                        'userId' => $PostUserId,
                        'firstName' => $PostFirstName,
                        'lastName' => first_index($PostLastName),
                        'comments' => array()
                    );

                    array_push($posts_arr, $post_item);
                }

                if ($local_post_id !== $PostId) {
                    $local_post_id = $PostId;
                    $counter += 1;
                }

                if (is_numeric($CommentId)) {
                    $comment_arr = array(
                        'commentId' => $CommentId,
                        'commentBody' => $CommentBody,
                        'isEdited' => boolval($CommentIsEdited),
                        'datePosted' => $comm->format_date_to_string($CommentDatePosted),                        
                        'userId' => $CommentUserId,
                        'firstName' => $CommentFirstName,
                        'lastName' => first_index($CommentLastName),
                        'replies' => array()
                    );

                    array_push($posts_arr[$counter]['comments'], $comment_arr);
                }

                if ($local_comment_id !== $CommentId) {
                    $local_comment_id = $CommentId;
                    $comment_counter = 0;
                } else {
                    $comment_counter += 1;
                }

                if (is_numeric($ReplyId)) {
                    $reply_item = array(
                        'replyId' => $ReplyId,
                        'replyBody' => $ReplyBody,
                        'isEdited' => boolval($ReplyIsEdited),
                        'datePosted' => $comm->format_date_to_string($ReplyDatePosted),
                        'userId' => $ReplyUserId,
                        'firstName' => $ReplyFirstName,
                        'lastName' => $ReplyLastName
                    );

                    array_push($posts_arr[$counter]['comments'][$comment_counter]['replies'], $reply_item);
                }
            }

            http_response_code(200);
            print_r(json_encode($posts_arr));
        } else {
            http_response_code(404);
            echo custom_array('No posts found');
        }
    } else {
        http_response_code(400);
        echo custom_array($comm->status);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $comm->createError($e);
    echo custom_array($comm->err_message);
}