<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_user.php';
include '../../global_functions.php';

$user->user_id = set_id();
$user->user_first_name = $data->firstName ?? null;
$user->user_last_name = $data->lastName ?? null;
$user->email = $data->email ?? null;
$user->phone_num = $data->phoneNum ?? null;

$user->data_to_correct_format();
$user->data_is_empty();
$user->data_too_long();

if ($user->status === '') {
    if ($user->update()) {
        http_response_code(200);
        echo custom_array('user has been updated');
    } else {
        http_response_code(400);
        echo custom_array('user could not be updatd');
    }
} else {
    http_response_code(400);
    echo custom_array($user->status);
}