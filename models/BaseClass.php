<?php
class BaseClass {
    protected $conn;
    protected $stmt;
    protected $additional_query;
    protected $limit = ' LIMIT 0, 1';
    protected $row;
    protected $query;
    protected $cannot_empty = ' cannot not be empty';
    protected $too_long = ' can only be a maxiumn of 255 characters';

    public $user_id;
    public $user_first_name;
    public $user_last_name;

    public $is_active;

    public $company_id;
    public $company_name;

    public $type_id;
    public $type_name;

    public $date_due;

    public $status;

    public function currency($value) {
        return '$' . $value;
    }

    public function pay($amount, $id, $type_id) {
        $this->stmt = $this->prepare_stmt("CALL updPayExpense('{$id}', '{$amount}', '{$type_id}');");

        return $this->stmt_executed();
    }

    public function is_paid($id) {
        $this->stmt = $this->prepare_stmt('SELECT IsPaid from paymenthistory where ExpenseId = ' . $id);
        $this->execute();
        return $this->convert_to_boolean($this->stmt->fetchColumn());
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

    protected function execute() {
        $this->stmt->execute();
    }

    protected function additional_query_empty() {
        if ($this->additional_query === '' || $this->additional_query === null) {
            $this->additional_query = ' WHERE ';
        } else {
            $this->additional_query .= ' AND ';
        }
    }

    protected function convert_to_boolean($value) {
        if (gettype($value) === 'boolean') {
            return $value;
        } else if (gettype($value) === 'integer') {
            if ($value === 0) {
                return false;
            } else if ($value === 1) {
                return true;
            }
        } else {
            http_response_code(400);
            custom_array('not valid');
        }
    }

    protected function format_status() {
        if ($this->status !== '') {
            $this->status .= ' and ';
        }
    }
}