<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../partail_files/jwt_partial.php';

$by_user = false;
$by_active = false;
$by_company = false;

if (get_isset('userId')) {
    $loan->user_id = set_get_variable('userId');
    $by_user = true;
}

if (get_isset('isActive')) {
    $loan->is_active = set_get_variable('isActive');
    $by_active = true;
}

if (get_isset('companyId')) {
    $loan->company_id = set_get_variable('companyId');
    $by_company = true;
}

$result = $loan->get_all($by_user, $by_company, $by_active);

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

    echo json_encode($loan_arr);
} else {
    echo custom_array('No loans found');
}

http_response_code(200);