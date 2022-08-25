<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_reply.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';
include '../../models/IdTypes.php';

try {
    $reply->user_id = $decoded->userId;

    if (get_isset('userId')) {
        $reply->user_id = set_get_variable('userId');
    } else {
        $reply->user_id = null;
    }

    if (get_isset('commentId')) {
        $reply->comment_id = set_get_variable('commentId');
    } else {
        $reply->comment_id = null;
    }

    if (get_isset('isEdited')) {
        $reply->is_edited = set_get_variable('isEdited');
    } else {
        $reply->is_edited = null;
    }
    $reply->validate_id(IdTypes::UserId, true);
    $reply->validate_id(IdTypes::CommentId, true);
    $reply->validate_boolean(BooleanTypes::IsEdited, true);

    if ($reply->status_is_empty()) {
        if ($decoded->userId !== $reply->user_id && !$decoded->isAdmin) {
            http_response_code(400);
            echo custom_array(Reply::$not_auth);
            die();
        }

        $results = $reply->get_all();
        $count = $results->rowCount();

        if ($count > 0) {
            $reply_array = array();

            while($row = $results->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $reply_arr = array(
                    'replyId' => $ReplyId,
                    'replyBody' => $ReplyBody,
                    'isEdited' => boolval($IsEdited),
                    'datePosted' => $reply->format_date_to_string($DatePosted),
                    'commentId' => $CommentId
                );
        
                if ($decoded->userId !== $reply->user_id) {
                    $reply_arr['userId'] = $this->user_id;
                    $reply_arr['firstNamee'] = $this->user_first_name;
                    $reply_arr['lastName'] = $this->user_last_name;
                }
                array_push($reply_array, $reply_arr);
            }

            http_response_code(500);
            print_r(json_encode(array(
                'replies' => $reply_array
            )));
        } else {
            http_response_code(404);
            echo custom_array('No replies found');
        }
    } else {
        http_response_code(400);
        echo custom_array($reply->status);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $reply->user_id = $decoded->userId;
    $reply->createError($e);
    echo custom_array($reply->err_message);
}