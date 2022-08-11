<?php
class Suggestion extends BaseClass {
    public int $suggestion_id;
    public string $suggestion_header;
    public string $suggestion_body;
    public string $date_submitted;
    public $approved_denied;
    public string $deny_reason;
    public int $approved_denied_by;
    public string $approved_denied_first_name;
    public string $approved_denied_last_name;

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

    public function suggestion_name_exists() {
        $this->query = 'SELECT EXISTS(SELECT SuggestionId FROM suggestions WHERE SuggestHeader = ' . $this->suggestion_header . ') AS SuggestionNameExists';

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

    public function suggestion_array(string $message = '', bool $include_user = false) {
        $suggest_arr = array(
            'suggestionId' => $this->suggestion_id,
            'suggestionHeader' => $this->suggestion_header,
            'suggestionBody' => $this->suggestion_body,
            'datePosted' => $this->date_submitted
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

    private function clean_data() {
        $this->suggestion_header = htmlspecialchars(strip_tags($this->suggestion_header));
        $this->suggestion_body = htmlspecialchars(strip_tags($this->suggestion_body));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
    }
}