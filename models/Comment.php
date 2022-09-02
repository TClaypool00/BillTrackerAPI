<?php
class Comment extends BaseCommunityClass {
    public $comment_body;
    public string $no_access = "The comment either doesn't exist or you don't have acces to it.";

    private string $select_all = 'SELECT * FROM vwcomments';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insComment('{$this->comment_body}', '{$this->user_id}', '{$this->type_id}', '{$this->parent_id}');");
        $this->execute();

        $this->comment_id = $this->stmt->fetchColumn();
    }

    public function update() {
        $this->clean_data();
        
        $this->stmt = $this->prepare_stmt("CALL updComment('{$this->comment_body}', '{$this->comment_id}');");

        return $this->stmt_executed();
    }

    public function get() {
        $this->stmt = $this->prepare_stmt($this->select_all . ' WHERE CommentId = ' . $this->comment_id);
        $this->execute();
        $this->retrieve_data();
    }

    public function get_all() {
        if ($this->user_id !== null) {
            $this->additional_query = ' WHERE UserId = ' . $this->user_id;
        }

        if ($this->parent_id !== null) {
            $this->additional_query_empty();
            $this->additional_query .= 'ParentId = ' . $this->parent_id;
        }
        
        if ($this->is_edited !== null) {
            $this->additional_query_empty();
            $this->additional_query .= 'IsEdited = ' . $this->is_edited;
        }

        if (!is_null($this->search)) {
            $this->additional_query_empty();
            $this->additional_query .= 'CommentBody LIKE %' . $this->comment_body . '%';
        }

        $this->limit_by_index();

        $this->stmt = $this->prepare_stmt($this->select_all . $this->additional_query);
        $this->execute();

        return $this->stmt;
    }

    public function  delete() {
        $this->stmt = $this->prepare_stmt("CALL delComment('{$this->comment_id}');");

        return $this->stmt_executed();
    }

    public function comment_array(bool $include_user_info = false, $message = null) {
        $comment_arr = array(
            'commentId' => $this->comment_id,
            'commentBody' => $this->comment_body,
            'datePosted' => $this->date_posted,
            'isEdited' => $this->is_edited,
            'parentId' => $this->parent_id
        );

        if ($include_user_info) {
            $this->first_index_string();
            $comment_arr['userId'] = $this->user_id;
            $comment_arr['firstName'] = $this->user_first_name;
            $comment_arr['lastName'] = $this->user_last_name;
        }

        if ($message !== null) {
            $comment_arr['message'] = $message;
        }

        return json_encode($comment_arr);
    }

    public function validate_body() {
        if ($this->comment_body_null()) {
            $this->format_status();
            $this->status .= 'Comment body' . $this->cannot_be_null;
        } else if (!is_string($this->comment_body)) {
            $this->comment_body = strval($this->comment_body);
        }

        if (!$this->comment_body_null() && strlen($this->comment_body) > 255) {
            $this->format_status();
            $this->status .= 'Comment body' . $this->too_long;
        }
    }
    
    private function retrieve_data() {
        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->comment_body = $this->row_value('CommentBody');
        $this->date_posted = $this->format_date_to_string($this->row_value('DatePosted'));
        $this->parent_id = $this->row_value('ParentId');
        $this->is_edited = boolval($this->row_value('IsEdited'));
        $this->user_id = $this->row_value('UserId');
        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
    }

    public function comment_body_null() {
        return $this->comment_body === null || $this->comment_body === '';
    }

    private function clean_data() {
        $this->comment_id = htmlspecialchars(strip_tags($this->comment_id));
        $this->comment_body = htmlspecialchars(strip_tags($this->comment_body));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->parent_id =htmlspecialchars(strip_tags($this->parent_id));
    }
}