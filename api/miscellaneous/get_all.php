<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_miscellaneous.php';
include '../../partail_files/jwt_partial.php';
include '../../models/BooleanTypes.php';

try {
    $start_date = null;
    $end_date = null;

    if (get_isset('userId')) {
        $misc->user_id = set_get_variable('userId');
    } else {
        $misc->user_id = null;
    }

    if (get_isset('companyId')) {
        $misc->company_id = set_get_variable('companyId');
    } else {
        $misc->company_id = null;
    }

    if (get_isset('startDate')) {
        $start_date = set_get_variable('startDate');
    } else {
        $start_date = null;
    }

    if (get_isset('endDate')) {
        $end_date = set_get_variable('endDate');
    } else {
        $end_date = null;
    }

    if (get_isset('showCurrency')) {
        $misc->show_currency = set_get_variable('showCurrency');
    } else {
        $misc->show_currency = false;
    }

    $misc->validate_user_id(true);
    $misc->validate_company_id(true);
    $misc->validate_boolean(BooleanTypes::ShowCurrency, true);

    if ($misc->status_is_empty()) {
        if (!$decoded->isAdmin) {
            if ($misc->misc_params_null($start_date, $end_date)) {
                http_response_code(403);
                echo custom_array(Miscellaneous::$all_params_null);
                die();
            }

            if (!is_null($misc->user_id)) {
                if ($decoded->userId !== $misc->user_id) {
                    http_response_code(403);
                    echo custom_array(Miscellaneous::$not_auth);
                    die();
                } else {
                    if (!is_null($misc->company_id) && !$misc->user_has_company()) {
                        http_response_code(403);
                        echo custom_array(Miscellaneous::$does_not_have_company);
                        die();
                    }
                }
            } else {
                http_response_code(403);
                echo custom_array(Miscellaneous::$user_id_null);
            }
        }

        $result = $misc->get_all($start_date, $end_date);

        $num = $result->rowCount();

        if ($num > 0) {
            $misc_arr = array();

            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                if ($misc->show_currency) {
                    $Amount = currency($Amount);
                }

                $misc_item = array(
                    'miscellaneousId' => $MiscellaneousId,
                    'name' => $Name,
                    'amount' => $Amount,
                    'companyId' => $CompanyId,
                    'companyName' => $CompanyName,
                    'userId' => $UserId,
                    'firstName' => $FirstName,
                    'lastName' => $LastName
                );

                array_push($misc_arr, $misc_item);
            }

            http_response_code(200);
            echo json_encode($misc_arr);
        } else {
            http_response_code(404);
            echo custom_array('No miscellaneous found');
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo custom_array($e->getMessage());
}