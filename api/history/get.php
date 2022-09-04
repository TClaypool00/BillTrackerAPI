<?php
include '../../partail_files/get_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_history.php';
include '../../partail_files/jwt_partial.php';

try {
    $history->payment_id = set_id();

    if ($history->history_exists()) {
        $history->get_type();

        if (is_numeric($history->type_id) && $history->type_id !== 0) {
            $history->get();

            if (!$decoded->isAdmin && $history->user_id !== $decoded->userId) {
                http_response_code(403);
                echo custom_array(History::$not_auth);
            } else {
                $payment_arr = array(
                    'paymentId' => $history->payment_id,
                    'expenseId' => $history->expense_id,
                    'isPaid' => $history->is_paid,
                    'isLate' => $history->is_late,
                    'dateDue' => $history->date_due,
                    'datePaid' => $history->date_paid,
                    'name' => $history->name,
                    'amount' => currency($history->amount),
                );

                if ($history->user_id !== $decoded->userId) {
                    $payment_arr['userId'] = $history->user_id;
                    $payment_arr['userFirstName'] = $history->user_first_name;
                    $payment_arr['userLastName'] = $history->user_last_name;
                }

                http_response_code(200);
                print_r(json_encode($payment_arr));
            }
        } else {
            http_response_code(500);
            echo custom_array('Something happend our end');
        }
    } else {
        http_response_code(404);
        echo custom_array('History item does not exists');
    }

} catch (Throwable $e) {
    http_response_code(500);
    $history->createError($e);
    echo custom_array($e->getMessage());
}