<?php
include '../../partail_files/create_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_loan.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';
include '../../models/IdTypes.php';
include '../../models/DateTypes.php';

try {
    define('MESSAGE', 'Loan has been created');
    $loan->loan_name = $data->loanName ?? null;
    $loan->monthly_amt_due = $data->monthlyAmtDue ?? null;
    $loan->total_loan_amt = $data->totalAmtDue ?? null;
    $loan->remaining_amt = $data->remaingAmt ?? null;
    $loan->company_id = $data->companyId ?? null;
    $loan->date_due = $data->dateDue ?? null;
    $loan->user_id = $decoded->userId;

    if (get_isset('returnObject')) {
        $loan->return_object = set_get_variable('returnObject');
    } else {
        $loan->return_object = false;
    }

    $loan->data_is_null();
    $loan->validate_date(DateType::DateDue);
    $loan->format_data();
    $loan->validate_id(IdTypes::CompanyId);
    $loan->validate_amount();
    $loan->validate_boolean(BooleanTypes::ReturnObject);

    if ($loan->status_is_empty()) {
        if (!$loan->user_has_company()) {
            http_response_code(403);
            echo custom_array(Loan::$does_not_have_company);
            die();
        }
        $loan->create();

        if (is_numeric($loan->loan_id) && $loan->loan_id !== 0) {
            http_response_code(201);
            if ($loan->return_object) {
                $loan->get();
                print_r($loan->loan_array(false, $loan->user_id !== $decoded->userId, MESSAGE, false));
            } else {
                echo custom_array(MESSAGE);
            }
        } else {
            http_response_code(400);
            echo custom_array('Loan could not be created');
        }
    } else {
        http_response_code(400);
        echo custom_array($loan->status);
    }
} catch (Throwable $e) {
    http_response_code(500);
    $loan->createError($e);
    echo custom_array($loan->err_message);
}