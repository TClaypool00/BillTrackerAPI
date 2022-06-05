<?php
include '../../partail_files/create_header.php';
include '../../partail_files/object_partial_files/new_user.php';
include '../../global_functions.php';
include '../../config/Secret.php';
require_once('../../vendor/autoload.php');
use \Firebase\JWT\JWT; 

$user->email = $data->email ?? null;
$password = $data->password ?? null;

$user->get(true, true);

if ($user->user_first_name != null) {
    if ($user->verify_password($password)) {

        $secret = new Secret($user->user_id, $user->isAdmin);

        $jwt= JWT::encode($secret->token, Secret::$key, Secret::$alg);

        $user_arr = array(
            'userId' => $user->user_id,
            'firstName' => $user->user_first_name,
            'lastName' => $user->user_last_name,
            'email' => $user->email,
            'phoneNum' => $user->phone_num,
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