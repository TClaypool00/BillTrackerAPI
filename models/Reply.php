<?php
class Reply extends BaseCommunityClass {
    public $reply_id;
    public $reply_body;
    public string $no_access = 'The reply either does not exist or you do not has access to it.';

    private $select_all = 'SELECT * FROM vwreplies';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insReply('{$this->reply_body}', {$this->comment_id}, '{$this->user_id}');");

        $this->execute();

        $this->reply_id = $this->stmt->fetchColumn();
    }

    public function update() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL updReply('{$this->reply_body}', {$this->reply_id});");

        return $this->stmt_executed();
    }

    public function get() {
        $this->query = $this->select_all . ' WHERE ReplyId = ' . $this->reply_id . $this->limit;

        $this->stmt = $this->prepare_stmt($this->query);

        $this->execute();

        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->reply_body = $this->row_value('ReplyBody');
        $this->date_posted = $this->format_date_to_string($this->row_value('DatePosted'));
        $this->is_edited = boolval($this->row_value('IsEdited'));
        $this->comment_id = $this->row_value('CommentId');
        $this->user_id = $this->row_value('UserId');
        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
    }

    public function get_all() {
        if ($this->is_edited !== null) {
            $this->additional_query_empty();
            $this->additional_query .= 'IsEdited = ' . $this->is_edited;
        }

        if ($this->comment_id !== null) {
            $this->additional_query_empty();
            $this->additional_query .= 'CommentId = ' . $this->comment_id;
        }

        if ($this->user_id !== null) {
            $this->additional_query_empty();
            $this->additional_query .= 'UserId = ' . $this->user_id;
        }

        if (!is_null($this->search)) {
            $this->additional_query_empty();
            $this->additional_query .= 'ReplyBody LIKE %' . $this->search . '%';
        }

        $this->limit_by_index();

        $this->stmt = $this->prepare_stmt($this->select_all . $this->additional_query);
        $this->execute();
        return $this->stmt;
    }

    public function delete() {
        $this->stmt = $this->prepare_stmt("CALL delReply('{$this->reply_id}');");

        return $this->stmt_executed();
    }

    public function has_access_reply($decoded) {
        $name = '';

        $this->query = 'SELECT EXISTS(SELECT ReplyId FROM replies WHERE ReplyId = ' . $this->reply_id;
        if (!$decoded->isAdmin) {
            $name = 'UserHasReply';
            $this->query .= ' AND UserId = ' . $this->user_id;
        } else {
            $name = 'ReplyExists';
        }

        $this->query .= ') AS ' . $name;

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return boolval($this->stmt->fetchColumn());
    }

    public function validate_body() {
        if (!is_null($this->reply_body)) {
            if (is_string($this->reply_body)) {
                if (strlen($this->reply_body) > 255) {
                    $this->status = 'Reply body' . $this->too_long;
                }
            } else {
                $this->reply_body = strval($this->reply_body);
            }
        } else {
            $this->status = 'Reply body' . $this->cannot_empty;
        }
    }

    public function reply_array(bool $include_user = false, $message = null, bool $full_last_name = false) {
        if (!$full_last_name) {
            $this->first_index_string();
        }

        $reply_arr = array(
            'replyId' => $this->reply_id,
            'replyBody' => $this->reply_body,
            'datePosted' => $this->date_posted,
            'commentId' => $this->comment_id
        );

        if ($include_user) {
            $reply_arr['userId'] = $this->user_id;
            $reply_arr['firstName'] = $this->user_first_name;
            $reply_arr['lastName'] = $this->user_last_name;
        }

        if ($message !== null) {
            $reply_arr['message'] = $message;
        }

        return json_encode($reply_arr);
    }

    private function clean_data() {
        $this->reply_id = htmlspecialchars(strip_tags($this->reply_id));
        $this->reply_body = htmlspecialchars(strip_tags($this->reply_body));
        $this->comment_id = htmlspecialchars(strip_tags($this->comment_id));
    }
}