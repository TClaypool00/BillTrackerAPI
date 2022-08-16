<?php
include '../../partail_files/get_all_header.php';
include '../../global_functions.php';
include '../../partail_files/object_partial_files/new_suggestion.php';
include '../../partail_files/jwt_partial.php';

try {
    if (get_isset('userId')) {
        $suggestion->user_id = set_get_variable('userId');
    } else {
        $suggestion->user_id = 0;
    }
    
    if (get_isset('option')) {
        $suggestion->option_string = set_get_variable('option');
    } else {
        $suggestion->approved_denied = '';
    }
    
    if (get_isset('approveDenyBy')) {
        $suggestion->approved_denied_by = set_get_variable('approveDenyBy');
    } else {
        $suggestion->approved_denied_by = 0;
    }

    if (!$decoded->isAdmin && $suggestion->user_id === 0) {
        if ($suggestion->approved_denied === '' || $suggestion->approved_denied_by === 0) {
            http_response_code(403);
            echo custom_array(Suggestion::$all_params_null);
            die();
        } else {
            http_response_code(403);
            echo custom_array(Suggestion::$user_id_null);
        }
    }

    $suggestion->validate_user_id(true);
    $suggestion->validate_option();

    if ($suggestion->status_is_empty()) {
        $result = $suggestion->get_all();

        $num = $result->rowCount();

        if ($num > 0) {
            $suggestion_arr = array();
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $suggestion_item = array(
                    'suggestionId' => $SuggestionId,
                    'suggestionHeader' => $SuggestHeader,
                    'suggestionBody' => $SuggestBody,
                    'datePosted' => $DateSubmitted,
                    'suggestionOption' => $SuggestionOption,
                    'watingOption' => $WaitingOption,
                    'denyReason' => $DenyReason,
                    'approveDenyBy' => $ApproveDenyBy,
                    'approveDenyFirstName' => $FirstName,
                    'approveDenyLastName' => $LastName
                );

                array_push($suggestion_arr, $suggestion_item);
            }

            http_response_code(200);
            echo json_encode($suggestion_arr);
        } else {
            http_response_code(404);
            echo custom_array('Suggestions found');
        }
    } else {
        http_response_code(400);
        echo custom_array($suggestion->status);
    }
} catch (Exception $e) {
    http_response_code(500);
    $suggestion->createError($e);
    echo custom_array($suggestion->err_message);
}