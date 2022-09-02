<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_user.php';
include '../../partail_files/jwt_partial.php';
include '../../models/IdTypes.php';

try {
    $user->search = get_isset('search') ? set_get_variable('search') : null;
    $user->index = get_isset('index') ? set_get_variable('index') : null;

    $user->validate_id(IdTypes::Index, true);

    if ($user->status_is_empty()) {
        if ($decoded->isAdmin) {
            $result = $user->get_all();
            $num = $result->rowCount();
        
            if ($num > 0) {
                $user_arr = array();
                while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
        
                    $user_item = array(
                        'userId' => $UserId,
                        'firstName' => $FirstName,
                        'lastName' => $LastName,
                        'email' => $Email,
                        'password' => $Password,
                        'isAdmin' => $IsAdmin,
                        'profileId' => $ProfileId,
                        'monthlySalary' => $MonthlySalary,
                        'budget' => $Budget,
                        'savings' => $Savings
                    );
                    array_push($user_arr, $user_item);
                }
                http_response_code(200);
                echo json_encode($user_arr);
            } else {
                http_response_code(404);
                echo custom_array('No users fouond');
            }
        } else {
            http_response_code(403);
            echo custom_array('Only admins have access to this route');
        }
    } else {
        http_response_code(400);
        echo custom_array($user->status);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $user->createError($e);
    echo custom_array($user->err_message);
}