<?php
class TrackerError extends BaseClass {
    public $error_id;
    public $message;
    public $code;
    public $line;
    public $stack_trace;
    public $num_users;
    public string $error_not_found = 'Error does not exist' ;

    private $select_all = 'SELECT * FROM vwerrors';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function get() {
        $this->query = $this->select_all . ' WHERE ErrorId = ' . $this->error_id . $this->limit;
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);
        $this->message = $this->row_value('ErrorMessage');
        $this->code = $this->row_value('ErrorCode');
        $this->line = $this->row_value('ErrorLine');
        $this->stack_trace = $this->row_value('StackTrace');
        $this->num_users = $this->row_value('UsersCount');
    }

    public function get_all() {
        $this->stmt = $this->prepare_stmt($this->select_all);
        $this->execute();

        return $this->stmt;
    }
    
    public function delete()
    {
        $this->stmt = $this->prepare_stmt("CALL delError('{$this->error_id}');");
        
        return $this->stmt_executed();
    }

    public function error_exists() {
        $this->query = 'SELECT EXISTS (' . $this->select_all . ' WHERE ErrorId = ' . $this->error_id . ') AS ErrorExists';
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return boolval($this->stmt->fetchColumn());
    }
}