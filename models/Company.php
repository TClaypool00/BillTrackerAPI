<?php
class Company extends BaseClass {
    public $type_id;
    public $type_name;

    private $select_all = 'SELECT * FROM vwcompanies';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insCompany('{$this->company_name}', '{$this->user_id}');");

        return $this->stmt_executed();
    }

    public function update() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL updCompanyName('{$this->company_name}', '{$this->company_id}');");

        return $this->stmt_executed();
    }

    public function get() {
        $this->query = $this->select_all . ' WHERE CompanyId = ' . $this->company_id . $this->limit;

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->company_name = $this->row_value('CompanyName');
        $this->type_id = $this->row_value('TypeId');
        $this->user_id = $this->row_value('UserId');
        $this->is_active = $this->row_value('IsActive');
        $this->type_name = $this->row_value('TypeName');
        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
    }

    public function get_all() {
        if ($this->user_id !== null) {
            $this->additional_query = ' WHERE UserId = ' . $this->user_id;
        }

        if ($this->type_id != null) {
            $this->additional_query_empty();
            $this->additional_query .= ' TypeId = ' . $this->type_id;
        }

        if ($this->is_active != null) {
            $this->additional_query_empty();
            $this->additional_query .= ' IsActive = ' . $this->is_active;
        }

        if (!is_null($this->search)) {
            $this->additional_query_empty();
            $this->additional_query .= 'CompanyName LIKE %' . $this->company_name . '%';
        }

        $this->limit_by_index();

        $this->stmt = $this->prepare_stmt($this->select_all . $this->additional_query);

        $this->execute();

        return $this->stmt;
    }

    public function data_is_null() {
        if ($this->company_name === null || $this->company_name === '') {
            $this->format_status();
            $this->status .= 'Company name' . $this->cannot_be_null;
        }
    }

    public function format_data() {
        $this->company_name = strval($this->company_name);
    }

    public function validate_data() {
        if (strlen($this->company_name) > 255) {
            $this->format_status();
            $this->status .= 'Company name' . $this->too_long;
        }
    }

    public function all_data_empty() {
        if ($this->user_id === null && $this->type_id === null && $this->is_active === null) {
            return true;
        }

        return false;
    }

    private function clean_data() {
        $this->company_name = htmlspecialchars(strip_tags($this->company_name));
        $this->type_id = htmlspecialchars(strip_tags($this->type_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
    }
}