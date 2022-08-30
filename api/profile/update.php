<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_profile.php';
include '../../partail_files/jwt_partial.php';
include '../../models/IdTypes.php';

try {
    $profile->profile_id = set_id();
    $profile->user_id = $decoded->userId;
    $profile->savings = $data->savings ?? null;
    $profile->monthly_salary = $data->salary ?? null;

    $profile->data_is_null();
    $profile->format_data();
    $profile->validate_data();

    if ($profile->status_is_empty()) {
        if ($profile->user_has_profile()) {
            $profile->update();
            if (is_numeric($profile->budget) && $profile->budget !== 0) {
                http_response_code(200);
                echo $profile->profile_array('Profile has been updated');
            } else {
                http_response_code(400);
                echo custom_array('Profile could not be updated');
            }
        } else {
            http_response_code(403);
            echo custom_array('You do have access to this profile');
        }
    } else {
        http_response_code(400);
        echo custom_array($profile->status);
    }
} catch (Exception $e) {
    http_response_code(500);
    $profile->createError($e);
    echo custom_array($profile->err_message);
}