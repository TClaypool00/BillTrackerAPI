<?php
include '../../partail_files/get_all_header.php';
include '../../partail_files/object_partial_files/new_miscellaneous.php';
include '../../global_functions.php';

$start_date = null;
$end_date = null;

if (get_isset('userId')) {
    $misc->user_id = set_get_variable('userId');
} else {
    $misc->user_id = null;
}

if (get_isset('companyId')) {
    $misc->company_id = set_get_variable('companyId');
} else {
    $misc->company_id = null;
}

if (get_isset('startDate')) {
    $start_date = set_get_variable('startDate');
} else {
    $start_date = null;
}

if (get_isset('endDate')) {
    $end_date = set_get_variable('endDate');
} else {
    $end_date = null;
}

$result = $misc->get_all($start_date, $end_date);

$num = $result->rowCount();

if ($num > 0) {
    $misc_arr = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $misc_item = array(
            'miscellaneousId' => $MiscellaneousId,
            'name' => $Name,
            'amount' => $Amount,
            'companyId' => $CompanyId,
            'companyName' => $CompanyName,
            'userId' => $UserId,
            'firstName' => $FirstName,
            'lastName' => $LastName
        );

        array_push($misc_arr, $misc_item);
    }

    http_response_code(200);
    echo json_encode($misc_arr);
} else {
    http_response_code(404);
    echo custom_array('No miscellaneous found');
}