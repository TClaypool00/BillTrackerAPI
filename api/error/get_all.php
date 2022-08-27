<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_error.php';
include '../../partail_files/jwt_partial.php';

try {
    $error->user_id = $decoded->userId;
    if ($decoded->isAdmin) {
        $result = $error->get_all();
        $num = $result->rowCount();

        if ($num > 0) {
            $errs_arr = array();
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $err_item = array(
                    'errorId' => $ErrorId,
                    'errorMessage' => $ErrorMessage,
                    'errorCode' => $ErrorCode,
                    'errorLine' => $ErrorLine,
                    'stackTrace' => $StackTrace,
                    'usersCount' => $UsersCount
                );

                array_push($errs_arr, $err_item);
            }

            http_response_code(200);
            print_r(json_encode($errs_arr));
        } else {
            http_response_code(404);
            echo custom_array('No errors found');
        }
    } else {
        http_response_code(403);
        echo custom_array(TrackerError::$not_auth);
    }
} catch(Throwable $e) {
    http_response_code(500);
    $error->createError($e);
    echo custom_array($error->err_message);
}