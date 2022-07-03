<?php
include '../../partail_files/update_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_profile.php';
include '../../partail_files/jwt_partial.php';

$profile->monthly_salary = $data->monthlySalary ?? null;
$profile->budget = $data->budget ?? null;
$profile->savings = $data->savings ?? null;