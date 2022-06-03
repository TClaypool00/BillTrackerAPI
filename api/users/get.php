<?php
include '../../partail_files/get_header.php';
include '../../partail_files/object_partial_files/new_user.php';
include '../../global_functions.php';

$user->user_id = set_id();
$show_password = false;

if (get_isset('showPassword')) {
    $show_password = set_get_variable('showPassword');
}

$user->get($show_password, false);

if ($user->user_first_name != null) {
    $user_arr = array(
        'userId' => $user->user_id,
        'firstName' => $user->user_first_name,
        'lastName' => $user->user_last_name,
        'email' => $user->email,
        'phoneNum' => $user->phone_num,
        'password' => $user->password,
        'isAdmin' => $user->isAdmin
    );

    http_response_code(200);
    print_r(json_encode($user_arr));
} else {
    http_response_code(404);
    echo custom_array('user does not exist');
}