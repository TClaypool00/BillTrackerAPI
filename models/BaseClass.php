<?php
class BaseClass extends ValidateClass {
    public $err_message = 'An error has occured. We apologize for the inconvience. We will fix it as soon as possible.';

    protected $conn;
    protected $stmt;
    protected $additional_query;
    protected $limit = ' LIMIT 0, 1';
    protected $row;
    protected $query;
    protected $time_stamp;
    protected $error_message;
    protected $stack_trace;

    public function pay($id, $type_id) {
        $this->stmt = $this->prepare_stmt("CALL updPayExpense('{$id}', '{$type_id}');");

        return $this->stmt_executed();
    }

    public function is_paid($id, $type_id) {
        $this->stmt = $this->prepare_stmt('SELECT IsPaid from paymenthistory where ExpenseId = ' . $id . ' AND TypeId = ' . $type_id . ' AND (MONTH(DateDue) = MONTH(NOW()) AND YEAR(DateDue) = YEAR(NOW()))');
        $this->execute();
        return $this->convert_to_boolean($this->stmt->fetchColumn());
    }

    public function user_has_company() {
        $this->query = 'SELECT EXISTS(SELECT * FROM companies WHERE CompanyId = ' . $this->company_id . ' AND UserId = ' . $this->user_id . ') AS UserCompany';
        $this->stmt = $this->prepare_stmt($this->query);
        
        $this->execute();
        return boolval($this->stmt->fetchColumn());
    }

    public function all_params_null() {
        if ($this->is_active === null && $this->user_id === null && $this->is_paid === null && $this->is_late === null && $this->company_id === null && $this->date_due === null && $this->search === null) {
            return true;
        }

        return false;
    }

    public function drop_down() {
        $this->stmt = $this->prepare_stmt("CALL selCompanyDropDown('{$this->user_id}');");

        $this->execute();

        $result = $this->stmt;

        $num = $result->rowCount();
        $company_arr = array();
        $default_value = array('companyId' => '', 'companyName' => 'Please select a company');

        array_push($company_arr, $default_value);

        if ($num > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
        
                $company_item = array(
                    'companyId' => $CompanyId,
                    'companyName' => $CompanyName
                );
        
                array_push($company_arr, $company_item);
            }
        }

        return $company_arr;
    }

    public function createError(Exception $e) {
        $this->error_message = $e->getMessage();
        $this->stack_trace = $e->getTraceAsString();

        $this->error_message = str_replace("'", '', $this->error_message);
        $this->stack_trace = str_replace("'", '', $this->stack_trace);

        $this->query = "CALL insError('{$this->error_message}', '{$e->getCode()}', '{$e->getLine()}', '{$this->stack_trace}', '{$this->user_id}');";

        $this->stmt = $this->prepare_stmt($this->query);

        $this->execute();
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

    protected function format_date_to_string(string $date_as_string) {
        $date = $this->date_from_string($date_as_string);

        return date_format($date, 'D M d, Y g:i a');
    }

    protected function format_date(string $date_as_string) {
        $date = $this->date_from_string($date_as_string);

        return date_format($date, 'D M d, Y');
    }

    protected function date_from_string(string $date) {
        return date_create($date);
    }
}