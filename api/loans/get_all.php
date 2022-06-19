<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../partail_files/jwt_partial.php';

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

$loan->validate_user_id(true);
$loan->validate_company_id(true);
$loan->validate_is_active(true);

if ($loan->status === '') {
    if (!$decoded->isAdmin) {
        if ($decoded->userId !== $loan->user_id) {
            http_response_code(403);
            echo custom_array(Loan::$not_auth);
            die();
        } else {
            if (!$loan->user_has_company()) {
                http_response_code(403);
                echo custom_array(Loan::$does_not_have_company);
                die();
            }
        }
    }

    $result = $loan->get_all();

    $num = $result->rowCount();

    if ($num > 0) {
        $loan_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $loan_item = array(
                'loanId' => $LoanId,
                'loanName' => $LoanName,
                'isActive' => $IsActive,
                'monthlyAmountDue' => $MonthlyAmountDue,
                'totalAmountDue' => $TotalAmountDue,
                'remainingAmount' => $RemainingAmount,
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