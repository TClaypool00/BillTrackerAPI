<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';

if (get_isset('userId')) {
    $loan->user_id = set_get_variable('userId');
} else {
    $loan->user_id = null;
}

if (get_isset('isActive')) {
    $loan->is_active = set_get_variable('isActive');
} else {
    $loan->is_active = null;
}

if (get_isset('companyId')) {
    $loan->company_id = set_get_variable('companyId');
} else {
    $loan->company_id = null;
}

if (get_isset('isPaid')) {
    $loan->is_paid = set_get_variable('isPaid');
} else {
    $loan->is_paid = null;
}

if (get_isset('isLate')) {
    $loan->is_late = set_get_variable('isLate');
} else {
    $loan->is_late = null;
}

if (get_isset('showCurrency')) {
    $loan->show_currency = set_get_variable('showCurrency');
} else {
    $loan->show_currency = false;
}

$loan->validate_user_id(true);
$loan->validate_company_id(true);
$loan->validate_is_active(true);
$loan->validate_boolean(BooleanTypes::IsLate, true);
$loan->validate_boolean(BooleanTypes::IsPaid, true);
$loan->validate_boolean(BooleanTypes::ShowCurrency);

if ($loan->status_is_empty()) {
    if (!$decoded->isAdmin) {
        if ($loan->all_params_null()) {
            http_response_code(403);
            echo custom_array(Loan::$all_params_null);
            die();
        }

        if (!is_null($loan->user_id)) {
            if ($decoded->userId !== $loan->user_id) {
                http_response_code(403);
                echo custom_array(Loan::$not_auth);
                die();
            } else {
                if (!is_null($loan->company_id) && !$loan->user_has_company()) {
                    http_response_code(403);
                    echo custom_array(Loan::$does_not_have_company);
                    die();
                }
            }
        } else {
            http_response_code(403);
            echo custom_array(Loan::$user_id_null);
            die();
        }
    }

    $result = $loan->get_all();

    $num = $result->rowCount();

    if ($num > 0) {
        $loan_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            if ($loan->show_currency) {
                $MonthlyAmountDue = currency($MonthlyAmountDue);
                $TotalAmountDue = currency($TotalAmountDue);
                $RemainingAmount = currency($RemainingAmount);
            }

            $loan_item = array(
                'loanId' => $LoanId,
                'loanName' => $LoanName,
                'isActive' => boolval($IsActive),
                'monthlyAmountDue' => $MonthlyAmountDue,
                'totalAmountDue' => $TotalAmountDue,
                'remainingAmount' => $RemainingAmount,
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

            array_push($loan_arr, $loan_item);
        }

        http_response_code(200);
        echo json_encode($loan_arr);
    } else {
        http_response_code(404);
        echo custom_array('No loans found');
    }
} else {
    http_response_code(400);
    echo custom_array($loan->status);
}