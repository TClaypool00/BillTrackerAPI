<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_profile.php';
include '../../partail_files/jwt_partial.php';

$profile->profile_id = set_id();
$profile->monthly_salary = $data->monthlySalary ?? null;
$profile->budget = $data->budget ?? null;
$profile->savings = $data->savings ?? null;

$profile->data_is_null();
$profile->format_data();
$profile->validate_data();

if ($profile->status_is_empty()) {
    if ($profile->profile_exists()) {
        $profile->user_id = $decoded->userId;
        if ($profile->user_has_profile()) {
            if ($profile->update()) {
                http_response_code(200);
                echo custom_array('Profile has been updated');
            } else {
                http_response_code(400);
                echo custom_array('Profile could not be updated');
            }
        } else {
            http_response_code(403);
            echo custom_array('You do have access to this profile');
        }
    } else {
        http_response_code(404);
        echo custom_array('Profile does not exists');
    }
} else {
    http_response_code(400);
    echo custom_array($profile->status);
}