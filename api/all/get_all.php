<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_all.php';
include '../../partail_files/jwt_partial.php';
include '../../models/IdTypes.php';

try {
    if (get_isset('userId')) {
        $all->user_id = set_get_variable('userId');
    } else {
        $all->user_id = null;
    }
    
    
    $all->validate_id(IdTypes::UserId);
    
    if ($all->status_is_empty()) {
        $arr = array();
        if (!$decoded->isAdmin && $decoded->userId !== $all->user_id) {
            http_response_code(403);
            echo custom_array(All::$not_auth);
            die();
        }
        $all_arr = array();
        for ($x = 0; $x <= 3; $x++) {
            $result = $all->get($x);
        
            $num = $result->rowCount();
        
            if ($num > 0) {
                while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $item_arr = array();
        
                    if ($x === 0) {
                        $item_arr['billId'] = $BillId;
                        $item_arr['billName'] = $BillName;
                        $item_arr['amountDue'] = currency($AmountDue);
                    } else if ($x === 1) {
                        $item_arr['loanId'] = $LoanId;
                        $item_arr['loanName'] = $LoanName;
                        $item_arr['monthlyAmountDue'] = currency($MonthlyAmountDue);
                        $item_arr['totalAmountDue'] = currency($TotalAmountDue);
                        $item_arr['remainingAmount'] = currency($RemainingAmount);
                    } else if ($x === 2) {
                        $item_arr['subscriptionId'] = $SubscriptionId;
                        $item_arr['subscriptionName'] = $Name;
                        $item_arr['amountDue'] = currency($AmountDue);
                    } else if ($x === 3) {
                        $item_arr['miscellaneousId'] = $MiscellaneousId;
                        $item_arr['name'] = $Name;
                        $item_arr['amount'] = currency($Amount);
                        $item_arr['dateAdded'] = $DateAdded;
                    }
        
                    if ($x !== 3) {
                        $item_arr['isActive'] = boolval($IsActive);
                        $item_arr['dateDue'] = $DateDue;
                        $item_arr['datePaid'] = $DatePaid;
                        $item_arr['isPaid'] = boolval($IsPaid);
                        $item_arr['isLate'] = boolval($IsLate);
                    }
        
                    $item_arr['companyId'] = $CompanyId;
                    $item_arr['companyName'] = $CompanyName;
                    if ($all->user_id !== $decoded->userId) {
                        $item_arr['userId'] = $UserId;
                        $item_arr['firstName'] = $FirstName;
                        $item_arr['lastName'] = $LastName;
                    }
    
                    array_push($arr, $item_arr);
                }
            }
            array_push($all_arr, $arr);
            $arr = array();
        }
    
        http_response_code(200);
        echo json_encode($all_arr);
    } else {
        http_response_code(400);
        echo custom_array($all->status);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo custom_array($e->getMessage());
}