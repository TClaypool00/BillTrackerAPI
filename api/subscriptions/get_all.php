<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_subscription.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';

try {
    if (get_isset('userId')) {
        $sub->user_id = set_get_variable('userId');
    } else {
        $sub->user_id = null;
    }
    
    if (get_isset('companyId')) {
        $sub->company_id = set_get_variable('companyId');
    } else {
        $sub->company_id = null;
    }
    
    if (get_isset('dateDue')) {
        $sub->due_date = set_get_variable('dateDue');
    } else {
        $sub->due_date = null;
    }
    
    if (get_isset('isPaid')) {
        $sub->is_paid = set_get_variable('isPaid');
    } else {
        $sub->is_paid = null;
    }
    
    if (get_isset('isLate')) {
        $sub->is_late = set_get_variable('isLate');
    } else {
        $sub->is_late = null;
    }
    
    if (get_isset('isActive')) {
        $sub->is_active = set_get_variable('isActive');
    } else {
        $sub->is_active = null;
    }
    
    if (get_isset('showCurrency')) {
        $sub->show_currency = set_get_variable('showCurrency');
    } else {
        $sub->show_currency = false;
    }
    
    $sub->validate_user_id(true);
    $sub->validate_company_id(true);
    $sub->validate_date(true);
    $sub->validate_boolean(BooleanTypes::IsPaid, true);
    $sub->validate_boolean(BooleanTypes::IsLate, true);
    $sub->validate_is_active(true);
    $sub->validate_boolean(BooleanTypes::ShowCurrency);
    
    if ($sub->status_is_empty()) {
        if (!$decoded->isAdmin) {
            if ($sub->all_params_null()) {
                http_response_code(403);
                echo custom_array(Subscription::$all_params_null);
                die();
            }
    
            if (is_null($sub->user_id)) {
                http_response_code(403);
                echo custom_array(Subscription::$user_id_null);
                die();
            } else {
                if (!is_null($sub->company_id) && !$sub->user_has_company()) {
                    http_response_code(403);
                    echo custom_array(Subscription::$does_not_have_company);
                }
            }
        }
    
        $result = $sub->get_all();
        $num = $result->rowCount();
    
        if ($num > 0) {
            $sub_arr = array();
    
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
    
                if ($sub->show_currency) {
                    $AmountDue = currency($AmountDue);
                }
    
                $sub_item = array(
                    'subscriptionId' => $SubscriptionId,
                    'name' => $Name,
                    'amountDue' => $AmountDue,
                    'dueDate' => $DueDate,
                    'isActive' => boolval($IsActive),
                    'dateDue' => $DateDue,
                    'datePaid' => $DatePaid,
                    'isPaid' => boolval($IsPaid),
                    'isLate' => boolval($IsLate),
                    'companyId' => $CompanyId,
                    'companyName' => $CompanyName,
                    'userId' => $UserId,
                    'firstName' => $FirstName,
                    'lastName' => $LastName
                );
                
                array_push($sub_arr, $sub_item);
            }
            http_response_code(200);
            echo json_encode($sub_arr);
        } else {
            http_response_code(404);
            echo custom_array('No subscriptions found');
        }
    } else {
        http_response_code(400);
        echo custom_array($sub->status);
    }
} catch (Exception $e) {
    http_response_code(500);
    $sub->createError($e);
    echo custom_array($sub->err_message);
}