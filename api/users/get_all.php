<?php
include '../../partail_files/get_all_header.php';
include '../../partail_files/object_partial_files/new_user.php';
include '../../global_functions.php';

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
            'isAdmin' => $IsAdmin
        );
        array_push($user_arr, $user_item);
    }
    http_response_code(200);
    echo json_encode($user_arr);
} else {
    http_response_code(404);
    echo custom_array('No users fouond');
}