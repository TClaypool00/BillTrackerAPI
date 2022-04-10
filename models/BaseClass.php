<?php
class BaseClass {
    protected $conn;
    protected $stmt;
    protected $select_all;
    protected $additional_query;
    protected $limit = ' LIMIT 0, 1';
    protected $row;

    public $user_id;
    public $user_firstName;
    public $user_last_name;

    protected function get_row_count() {
        return $this->stmt->rowCount();
    }

    protected function stmt_executed() {
        if ($this->stmt->execute()) {
            return true;
        }

        printf('Error: %s \n', $this->stmt->error);

        return false;
    }

    protected function prepare_stmt($statement) {
        return $this->conn->prepare($statement);
    }

    protected function row_value($value) {
        return $this->row[$value] ?? null;
    }
}