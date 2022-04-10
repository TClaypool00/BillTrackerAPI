<?php
include '../../partail_files/create_header.php';
include '../../partail_files/object_partial_files/new_user.php';
include '../../global_functions.php';

$user->user_firstName = $data->firstName;
$user->user_last_name = $data->lastName;
$user->email = $data->email;
$user->phone_num = $data->phoneNum;
$user->password = $data->password;
$user->confirm_password = $data->confirmPassword;

if (!$user->passwords_confirm()) {
    http_response_code(400);
    echo custom_array('passwords do not match');
    die();
}

if (!$user->password_meets_requirements()) {
    http_response_code(400);
    echo custom_array('password does not meet the minimum requirements');
    die();
}

if ($user->create()) {
    http_response_code(201);
    echo custom_array('user has been registered');
} else {
    http_response_code(400);
    echo custom_array('user could not be registered');
}