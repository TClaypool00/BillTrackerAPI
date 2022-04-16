<?php
class BaseClass {
    protected $conn;
    protected $stmt;
    protected $additional_query;
    protected $limit = ' LIMIT 0, 1';
    protected $row;
    protected $query;

    public $user_id;
    public $user_first_name;
    public $user_last_name;

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

    protected function execute() {
        $this->stmt->execute();
    }

    protected function currency($value) {
        return '$' . $value;
    }
}