<?php
class BaseClass extends ValidateClass {
    protected $conn;
    protected $stmt;
    protected $additional_query;
    protected $limit = ' LIMIT 0, 1';
    protected $row;
    protected $query;
    protected $time_stamp;

    public function pay($amount, $id, $type_id) {
        $this->stmt = $this->prepare_stmt("CALL updPayExpense('{$id}', '{$amount}', '{$type_id}');");

        return $this->stmt_executed();
    }

    public function is_paid($id) {
        $this->stmt = $this->prepare_stmt('SELECT IsPaid from paymenthistory where ExpenseId = ' . $id);
        $this->execute();
        return $this->convert_to_boolean($this->stmt->fetchColumn());
    }

    public function user_has_company() {
        $this->stmt = $this->prepare_stmt('SELECT EXISTS(SELECT * FROM companies WHERE CompanyId = ' . $this->company_id . ' AND UserId =' . $this->user_id . ') AS UserCompany');
        $this->execute();
        return $this->convert_to_boolean($this->stmt->fetchColumn());
    }

    public function all_params_null() {
        if ($this->is_active === null && $this->user_id === null && $this->is_paid === null && $this->is_late === null && $this->company_id === null && $this->date_due === null) {
            return true;
        }

        return false;
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

    protected function base_get() {
        $this->is_active = $this->row_value('IsActive');
        $this->date_due = $this->row_value('DateDue');
        $this->date_paid = $this->row_value('DatePaid');
        $this->is_paid = $this->row_value('IsPaid');
        $this->is_late = $this->row_value('IsLate');
        $this->company_id = $this->row_value('CompanyId');
        $this->company_name = $this->row_value('CompanyName');
        $this->user_id = $this->row_value('UserId');
        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
    }
}