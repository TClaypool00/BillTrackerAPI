<?php
include '../../partail_files/update_header.php';
include '../../partail_files/object_partial_files/new_miscellaneous.php';
include '../../global_functions.php';


$misc->miscellaneous_id = set_id();
$misc->name = $data->name;
$misc->amount = $data->amount;
$misc->company_id = $data->companyId;

if($misc->update()) {
    http_response_code(200);
    echo custom_array('Miscellaneous has been updated');
} else {
    http_response_code(400);
    echo custom_array('Miscellaneous could not be updated');
}