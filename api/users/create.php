<?php
include '../../partail_files/create_header.php';
include '../../partail_files/object_partial_files/new_user.php';
include '../../global_functions.php';

try {
    $user->user_first_name = $data->firstName ?? null;
    $user->user_last_name = $data->lastName ?? null;
    $user->email = $data->email ?? null;
    $user->phone_num = $data->phoneNum ?? null;
    $user->password = $data->password ?? null;
    $user->confirm_password = $data->confirmPassword ?? null;

    $user->data_to_correct_format();
    $user->data_is_empty();
    $user->data_too_long();

    if ($user->status === '') {
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
    } else {
        http_response_code(400);
        echo custom_array($user->status);
    }
} catch (Exception $e) {
    http_response_code(500);
    $user->createError($e);
    echo custom_array($user->err_message);
}