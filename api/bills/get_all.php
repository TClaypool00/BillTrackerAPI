<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_bill.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';

if (get_isset('userId')) {
    $bill->user_id = set_get_variable('userId');
} else {
    $bill->user_id = null;
}

if (get_isset('isActive')) {
    $bill->is_active = set_get_variable('isActive');
} else {
    $bill->is_active = null;
}

if (get_isset('companyId')) {
    $bill->company_id = set_get_variable('companyId');
} else {
    $bill->company_id = null;
}

if (get_isset('isPaid')) {
    $bill->is_paid = set_get_variable('isPaid');
} else {
    $bill->is_paid = null;
}

if (get_isset('isLate')) {
    $bill->is_late = set_get_variable('isLate');
} else {
    $bill->is_late = null;
}

if (get_isset('showCurrency')) {
    $bill->show_currency = set_get_variable('showCurrency');
} else {
    $bill->show_currency = false;
}

$bill->validate_user_id(true);
$bill->validate_is_active(true);
$bill->validate_company_id(true);
$bill->validate_boolean(BooleanTypes::IsPaid, true);
$bill->validate_boolean(BooleanTypes::IsLate, true);
$bill->validate_boolean(BooleanTypes::ShowCurrency, true);

if ($bill->status_is_empty()) {
    if (!$decoded->isAdmin) {
        if ($bill->all_params_null()) {
            http_response_code(403);
            echo custom_array(Bill::$all_params_null);
            die();
        }

        if (!is_null($bill->user_id)) {
            if ($decoded->userId !== $bill->user_id) {
                http_response_code(403);
                echo custom_array(Bill::$not_auth);
                die();
            }

            if (!is_null($bill->company_id) && !$bill->user_has_company()) {
                http_response_code(403);
                echo custom_array(Bill::$does_not_have_company);
                die();
            }
        } else {
            http_response_code(403);
            echo custom_array(Bill::$user_id_null);
            die();
        }
    }

    $result = $bill->get_all();

    $num = $result->rowCount();

    if ($num > 0) {
        $bill_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            if($bill->show_currency) {
                $AmountDue = currency($AmountDue);
            }

            $bill_item = array(
                'billId' => $BillId,
                'billName' => $BillName,
                'amountDue' => $AmountDue,
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
            array_push($bill_arr, $bill_item);
        }

        http_response_code(200);
        echo json_encode($bill_arr);
    } else {
        http_response_code(404);
        echo custom_array('No bills found');
    }
} else {
    http_response_code(404);
    echo custom_array($bill->status);
}