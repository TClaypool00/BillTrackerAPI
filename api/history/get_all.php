<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_history.php';
include '../../partail_files/jwt_partial.php';
include '../../models/IdTypes.php';

try {
    $history->user_id = get_isset('userId') ? set_get_variable('userId') : null;
    $history->index = get_isset('index') ? set_get_variable('index') : null;
    $history->type_id = get_isset('typeId') ? set_get_variable('typeId') : null;
    
    $history->expense_id = get_isset('expenseId') ? set_get_variable('expenseId') : null;

    $history->validate_id(IdTypes::UserId, true);

    if (!$decoded->isAdmin && (is_null($history->user_id) || $history->user_id !== $decoded->userId)) {
        http_response_code(400);
        echo custom_array(History::$not_auth);
        die();
    }

    if ((is_null($history->expense_id) && !is_null($history->type_id)) || (!is_null($history->expense_id) && is_null($history->type_id))) {
        http_response_code(400);
        echo custom_array('Both TypeId and ExpenseId cannot be null');
        die();
    }

    if ($history->status_is_empty()) {
        $result = $history->get_all();
        $num = $result->rowCount();

        if ($num > 0) {
            $over_all = array();
            $type_arr = array();
            $item_arr = array();
            $payment_arr = array();
            $history_arr = null;
            $current_type_id = null;
            $current_expense_id = null;
            $counter = -1;
            $times = 0;

            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                if (!array_key_exists($TypeName, $over_all)) {
                    $over_all[$TypeName] = array();
                }

                if (!in_array($ExpenseId, $over_all[$TypeName])) {
                    $history_arr = array();
                    $item_arr = array('id' => $ExpenseId, 'name' => $Name, 'history' => $history_arr);
                    array_push($over_all[$TypeName], $item_arr);
                }

                if ($current_type_id !== $TypeId) {
                    $current_type_id = $TypeId;
                    $counter = 0;
                } else {
                    if ($current_expense_id !== $ExpenseId) {
                        $current_expense_id = $ExpenseId;
                        $counter += 1;
                    }
                }

                $payment_arr = array('isPaid' => boolval($IsPaid), 'isLate' => boolval($IsLate), 'dateDue' => $history->format_date($DateDue), 'datePaid' => $history->format_date($DatePaid));
                array_push($over_all[$TypeName][$counter]['history'], $payment_arr);
            }

            print_r(json_encode($over_all));
        } else {
            http_response_code(404);
            echo custom_array('No history records found');
        }

    } else {
        http_response_code(400);
        echo custom_array($history->status);
    }



} catch (Throwable $e) {
    http_response_code(500);
    $history->user_id = $decoded->userId;
    $history->createError($e);
    echo custom_array($history->err_message);
}