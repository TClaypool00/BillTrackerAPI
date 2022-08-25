<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_comment.php';
include '../../partail_files/jwt_partial.php';
include '../../models/IdTypes.php';

try {
    $comment->user_id = $decoded->userId;

    if (get_isset('userId')) {
        $comment->user_id = set_get_variable('userId');
    } else {
        $comment->user_id = null;
    }

    if (get_isset('parentId')) {
        $comment->parent_id = set_get_variable('parentId');
    } else {
        $comment->parent_id = null;
    }

    if (get_isset('tyepId')) {
        $comment->type_id = set_get_variable('typeId');
    } else {
        $comment->type_id = null;
    }

    $comment->validate_id(IdTypes::UserId, true);
    $comment->validate_id(IdTypes::ParentId, true);
    $comment->validate_id(IdTypes::TypeId, true);

    if ($comment->status_is_empty()) {
        if ((is_null($comment->user_id) || $comment->user_id !== $decoded->userId) && !$decoded->isAdmin) {
            http_response_code(400);
            echo custom_array(Comment::$not_auth);
            die();
        }
        
        $result = $comment->get_all();
        $count = $result->rowCount();

        if ($count > 0) {
            $comment_arr = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $itme_arr = array(
                    'commentId' => $CommentId,
                    'commentBody' => $CommentBody,
                    'datePosted' => $comment->format_date_to_string($DatePosted),
                    'isEdited' => boolval($IsEdited),
                    'parentId' => $ParentId,
                    'typeId' => $TypeId,
                );

                if ($comment->user_id !== $decoded->userId) {
                    $itme_arr['userId'] = $UserId;
                    $itme_arr['firstName'] = $FirstName;
                    $itme_arr['lastName'] = $LastName;
                }

                array_push($comment_arr, $itme_arr);
            }

            http_response_code(200);
            print_r(json_encode(array(
                'comments' => $comment_arr
            )));
        } else {
            http_response_code(404);
            echo custom_array('No comments found');
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