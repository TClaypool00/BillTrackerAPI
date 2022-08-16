<?php
class Suggestion extends BaseClass {
    public int $suggestion_id;
    public $suggestion_header;
    public $suggestion_body;
    public $date_submitted;
    public $approved_denied;
    public $option_string;
    public $deny_reason;
    public $approved_denied_by;
    public $approved_denied_first_name;
    public $approved_denied_last_name;

    private string $select_all = 'SELECT * FROM vwsuggestions';

    public function __construct($db)
    {
        $this->conn = $db;
    }
    
    public function create() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insSuggestion('{$this->suggestion_header}', '{$this->suggestion_body}', '{$this->user_id}');");

        $this->execute();

        $this->suggestion_id = $this->stmt->fetchColumn();
    }

    public function update() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL updSuggestion('{$this->suggestion_header}', '{$this->suggestion_body}', '{$this->suggestion_id}');");

        return $this->stmt_executed();
    }

    public function get() {
        $this->query = $this->select_all . ' WHERE SuggestionId = ' . $this->suggestion_id . $this->limit;

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->suggestion_header = $this->row_value('SuggestHeader');
        $this->suggestion_body = $this->row_value('SuggestBody');
        $this->date_submitted = $this->row_value('DateSubmitted');
        $this->user_id = $this->row_value('AuthorId');
        $this->user_first_name = $this->row_value('AuthorFirstName');
        $this->user_last_name = $this->row_value('AuthorLastName');
        $this->approved_denied = $this->row_value('SuggestionOption');
        $this->option_string = $this->row_value('WaitingOption');
        $this->deny_reason = $this->row_value('DenyReason');
        $this->approved_denied_by = $this->row_value('ApproveDenyBy');
        $this->approved_denied_first_name = $this->row_value('FirstName');
        $this->approved_denied_last_name = $this->row_value('LastName');

        $this->date_submitted = $this->format_date_to_string($this->date_submitted);
    }

    public function get_all() {
        $this->query = $this->select_all;
        if ($this->user_id !== 0) {
            $this->additional_query = ' WHERE AuthorId = ' . ($this->user_id === null ? 'NULL' : $this->user_id);
        }

        if ($this->option_string !== '') {
            $this->additional_query_empty();
            $this->additional_query .= 'SuggestionOption = ' . $this->approved_denied;
        }

        if ($this->approved_denied_by !== 0) {
            $this->additional_query_empty();
            $this->additional_query .= 'ApproveDenyBy =' . ($this->approved_denied_by === null ? 'NULL' : $this->approved_denied_by);
        }

        $this->stmt = $this->prepare_stmt($this->select_all . $this->additional_query);

        $this->execute();

        return $this->stmt;
    }

    public function format_data() {
        $this->suggestion_header = strval($this->suggestion_header);
        $this->suggestion_body = strval($this->suggestion_body);
    }

    public function data_is_empty() {
        if ($this->suggestion_header === '' || $this->suggestion_header === null) {
            $this->format_status();
            $this->status .= 'Suggestion header' . $this->cannot_be_null;
        }

        if ($this->suggestion_body === '' || $this->suggestion_body === null) {
            $this->format_status();
            $this->status .= 'Suggestion body' . $this->cannot_be_null;
        }
    }

    public function data_too_long() {
        if (strlen($this->suggestion_header) > 255) {
            $this->format_status();
            $this->status .= 'Suggestion header' . $this->too_long;
        }
    }

    public function suggestion_name_exists(bool $is_udate = false) {
        $this->query = "SELECT EXISTS(SELECT SuggestionId FROM suggestions WHERE SuggestHeader = '" . $this->suggestion_header;

        if ($is_udate) {
            $this->query .= "' AND SuggestionId != " . $this->suggestion_id;
        }

        $this->query .= ") AS SuggestionNameExists";

        echo $this->query;

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return boolval($this->stmt->fetchColumn());
    }

    public function suggestion_exists() {
        $this->query = 'SELECT EXISTS(SELECT SuggestionId FROM suggestions WHERE SuggestionId = ' . $this->suggestion_id . ') AS SuggestionExists';

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return boolval($this->stmt->fetchColumn());
    }

    public function user_has_sugguestion() {
        $this->query = 'SELECT EXISTS(SELECT SuggestionId FROM suggestions WHERE SuggestionId = ' . $this->suggestion_id . ' AND  UserId = ' . $this->user_id . ') AS SuggestionExists';

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return boolval($this->stmt->fetchColumn());
    }

    public function suggestion_array(string $message = '', bool $include_user = false) {
        $suggest_arr = array(
            'suggestionId' => $this->suggestion_id,
            'suggestionHeader' => $this->suggestion_header,
            'suggestionBody' => $this->suggestion_body,
            'datePosted' => $this->date_submitted,
            'suggestionOption' => $this->approved_denied,
            'watingOption' => $this->option_string,
            'denyReason' => $this->deny_reason,
            'approveDenyBy' => $this->approved_denied_by,
            'approveDenyFirstName' => $this->approved_denied_first_name,
            'approveDenyLastName' => $this->approved_denied_last_name
        );

        if ($include_user) {
            $suggest_arr['authorId'] = $this->user_id;
            $suggest_arr['authorFirstName'] = $this->user_first_name;
            $suggest_arr['authorLastName'] = $this->user_last_name;
        }

        if ($message !== '') {
            $suggest_arr['message'] = $message;
        }

        return json_encode($suggest_arr);
    }

    public function not_found() {
        return 'A suggestion with the id of ' . $this->suggestion_id . ' was not found';
    }

    public function validate_option() {
        if (strtolower($this->option_string) === strtolower('approve')) {
            $this->approved_denied = 1;
        } else if (strtolower($this->option_string) === strtolower('deny')) {
            $this->approved_denied = 2;
        } else {
            $this->format_status();
            $this->status .= 'Option is not valid';
        }
    }

    private function clean_data() {
        $this->suggestion_header = htmlspecialchars(strip_tags($this->suggestion_header));
        $this->suggestion_body = htmlspecialchars(strip_tags($this->suggestion_body));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
    }
}