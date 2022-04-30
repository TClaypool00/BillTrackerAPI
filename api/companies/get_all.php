<?php
include '../../partail_files/get_all_header.php';
include '../../partail_files/object_partial_files/new_company.php';
include '../../global_functions.php';

$by_user = false;
$by_active = false;
$by_type = false;

if (get_isset('userId')) {
    $company->user_id = set_get_variable('userId');
    $by_user = true;
}

if (get_isset('isActive')) {
    $company->is_active = set_get_variable('isActive');
    $by_active = true;
}

if (get_isset('typeId')) {
    $company->type_id = set_get_variable('typeId');
    $by_type = true;
}

$result = $company->get_all($by_user, $by_type, $by_active);

$num = $result->rowCount();

if ($num > 0) {
    $company_arr = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $company_item = array(
            'companyId' => $CompanyId,
            'companyName' => $CompanyName,
            'isActive' => $IsActive,
            'typeId' => $TypeId,
            'typeName' => $TypeName,
            'firstName' => $FirstName,
            'lastName' => $LastName
        );

        array_push($company_arr, $company_item);
    }

    echo json_encode($company_arr);
} else {
    echo custom_array('No companines found');
}

http_response_code(200);