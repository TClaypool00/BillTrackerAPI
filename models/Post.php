<?php
class Post extends BaseClass {
    public $post_id;
    public $post_body;
    public $date_posted;

    private $post_body_string = 'Post body';
    private $select_all = 'SELECT * FROM vwposts';

    public static $post_does_not_exist = 'Post does not exist';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insPost('{$this->post_body}', '{$this->user_id}');");
        $this->execute();

        $this->fetch_data();
    }

    public function update() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL updPost('{$this->post_body}', '{$this->post_id}');");

        $this->execute();

        $this->fetch_data();
    }

    public function get() {
        $this->query = $this->select_all . ' WHERE PostId = ' . $this->post_id;
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();
        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->post_id = $this->row_value('PostId');
        $this->post_body = $this->row_value('PostBody');
        $this->date_posted = $this->format_date_to_string($this->row_value('DatePosted'));
        $this->is_edited = $this->row_value('IsEdited');
        $this->user_id = $this->row_value('UserId');
        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
    }

    public function get_all($start_date, $end_date) {
        if ($this->user_id !== null) {
            $this->additional_query = ' WHERE UserId = ' . $this->user_id;
        }

        if ($this->is_edited !== null) {
            $this->additional_query_empty();
            $this->additional_query .= 'IsEdited = ' . $this->is_edited;
        }

        if ($this->date_posted !== null) {
            $this->additional_query_empty();
            $this->additional_query .= 'DatePosted = ' . $this->date_posted;
        }

        if ($start_date != null && $end_date != null) {
            $this->additional_query_empty();

            $this->additional_query .= 'DatePosted BETWEEN ' . $start_date . ' AND ' . $end_date;
        } else if ($start_date != null || $end_date != null) {
            $this->additional_query_empty();
            $this->additional_query .= 'DatePosted = ';

            if ($start_date != null)  {
                $this->additional_query .= $start_date;
            }

            if ($end_date != null) {
                $this->additional_query .= $end_date;
            }
        }

        if (!is_null($this->search)) {
            $this->additional_query_empty();
            $this->additional_query .= 'PostBody LIKE %' . $this->search . '%';
        }

        $this->limit_by_index();

        $this->stmt = $this->prepare_stmt($this->select_all . $this->additional_query);
        $this->execute();
        return $this->stmt;
    }

    public function post_array($message = '', $user_info = true) {
        $post_arr = array(
            'PostId' => $this->post_id,
            'PostBody' => $this->post_body,
            'DatePosted' => $this->date_posted,
            'isEdited' => boolval($this->is_edited)
        );

        if (!$user_info) {
            $post_arr['UserId'] = $this->user_id;
            $post_arr['FirstName'] = $this->user_first_name;
            $post_arr['LastName'] = $this->user_last_name;
        }

        if ($message !== '') {
            $post_arr['message'] = $message;
        }        

        return json_encode($post_arr);
    }

    public function post_exists() {
        $this->query = 'SELECT EXISTS(SELECT p.PostId FROM posts p WHERE p.PostId = ' . $this->post_id . ') AS PostExists;';
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();
        return boolval($this->stmt->fetchColumn());
    }

    public function user_has_post() {
        $this->query = 'SELECT EXISTS(SELECT p.PostId FROM posts p WHERE p.PostId = ' . $this->post_id . ' AND p.UserId = ' . $this->user_id . ' ) AS UserHasPost;';
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();
        return boolval($this->stmt->fetchColumn());
    }

    public function data_is_null() {
        if (is_null($this->post_body)) {
            $this->status = $this->post_body_string . $this->cannot_be_null;
        } else {
            $this->post_body = strval($this->post_body);
        }
    }

    public function validate_data() {
        if (!is_null($this->post_body)) {
            if ($this->post_body === '') {
                $this->format_status();
                $this->status .= $this->post_body_string . $this->cannot_empty;
            }

            if (strlen($this->post_body) > 255) {
                $this->format_status();
                $this->status .= $this->post_body_string . $this->too_long;
            }
        }
    }

    private function clean_data() {
        $this->post_body = htmlspecialchars(strip_tags($this->post_body));
    }

    private function fetch_data() {
        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        if (!is_numeric($this->post_id)) {
            $this->post_id = $this->row_value('PostId');
        }

        $this->date_posted = $this->format_date_to_string($this->row_value('DatePosted'));
        $this->is_edited = boolval($this->row_value('IsEdited'));
    }
}