<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_user.php';
include '../../global_functions.php';

$user->user_id = set_id();
$user->user_first_name = $data->firstName;
$user->user_last_name = $data->lastName;
$user->email = $data->email;
$user->phone_num = $data->phoneNum;

if ($user->update()) {
    http_response_code(200);
    echo custom_array('user has been updated');
} else {
    http_response_code(400);
    echo custom_array('user could not be updatd');
}