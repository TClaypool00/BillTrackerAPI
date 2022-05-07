<?php
include '../../partail_files/create_header.php';
include '../../partail_files/object_partial_files/new_miscellaneous.php';
include '../../global_functions.php';

$misc->name = $data->name;
$misc->amount = $data->amount;
$misc->company_id = $data->companyId;

if ($misc->create()) {
    http_response_code(201);
    echo custom_array('Miscellaneous has been added');
} else {
    http_response_code(400);
    echo custom_array('Miscellaneous could not be added');
}