<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_company.php';
include '../../partail_files/jwt_partial.php';
include '../../models/IdTypes.php';
include '../../models/BooleanTypes.php';

try {
    if (get_isset('userId')) {
        $company->user_id = set_get_variable('userId');
    } else {
        $company->user_id = null;
    }
    
    if (get_isset('isActive')) {
        $company->is_active = set_get_variable('isActive');
    } else {
        $company->is_active = null;
    }
    
    if (get_isset('typeId')) {
        $company->type_id = set_get_variable('typeId');
    } else {
        $company->type_id = null;
    }
    
    $company->format_data(true);
    $company->validate_id(IdTypes::UserId, true);
    $company->validate_boolean(BooleanTypes::IsActive, true);
    
    
    if (!$decoded->isAdmin) {
        if ($company->all_data_empty()) {
            echo custom_array('You must an admin');
            die();
        }
    
        if ($decoded->userId !== $company->user_id) {
            echo custom_array('Only admins can choose other userId');
            die();
        }
    }
    
    $result = $company->get_all();
    
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
                'userId' => $UserId,
                'firstName' => $FirstName,
                'lastName' => $LastName
            );
    
            array_push($company_arr, $company_item);
        }
    
        http_response_code(200);
        echo json_encode($company_arr);
    } else {
        http_response_code(404);
        echo custom_array('No companines found');
    }
} catch(Exception $e) {
    http_response_code(500);
    $company->createError($e);
    echo custom_array($company->err_message);
}