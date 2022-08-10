<?php
include '../../partail_files/create_header.php';
include '../../partail_files/object_partial_files/new_user.php';
include '../../global_functions.php';
include '../../config/Secret.php';
require_once('../../vendor/autoload.php');
use \Firebase\JWT\JWT; 

try {
    $user->email = $data->email ?? null;
    $password = $data->password ?? null;

    $user->login_empty($password);
    $user->login_to_correct_format();
    $user->login_too_long();

    if ($user->status_is_empty()) {
        $user->get(true, true);

        if ($user->user_first_name != null) {
            if ($user->verify_password($password)) {

                $secret = new Secret($user->user_id, $user->isAdmin);

                $jwt= JWT::encode($secret->token, Secret::$key, Secret::$alg);

                $user_arr = array(
                    'userId' => $user->user_id,
                    'firstName' => $user->user_first_name,                
                    'isAdmin' => $user->isAdmin,
                    'token' => $jwt
                );

                http_response_code(200);
                echo json_encode(array(
                    'user' => $user_arr
                ));

            } else {
                http_response_code(400);
                echo custom_array('Password is not correct');
            }
        } else {
            http_response_code(404);
            echo custom_array('Incorrect email address');
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