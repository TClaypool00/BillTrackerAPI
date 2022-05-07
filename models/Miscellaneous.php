<?php
class Miscellaneous extends BaseClass {
    public $miscellaneous_id;
    public $name;
    public $amount;
    public $date_added;

    private $select_all = 'SELECT * FROM vwmiscellaneous';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create() {
        $this->clean_data();        

        $this->stmt = $this->prepare_stmt("CALL insMisc('{$this->name}', '{$this->amount}', '{$this->company_id}');");

        return $this->stmt_executed();
    }

    public function update() {
        $this->clean_data();
        
        $this->stmt = $this->prepare_stmt("CALL updMisc('{$this->name}', '{$this->amount}', '{$this->company_id}', '{$this->miscellaneous_id}');");
    
        return $this->stmt_executed();
    }

    public function delete() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL delMisc('{$this->miscellaneous_id}');");
    
        return $this->stmt_executed();
    }

    public function get() {
        $this->query = $this->select_all . ' WHERE MiscellaneousId = ' . $this->miscellaneous_id;
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->miscellaneous_id = $this->row_value('MiscellaneousId');
        $this->name = $this->row_value('Name');
        $this->amount = $this->row_value('Amount');
        $this->date_added = $this->row_value('DateAdded');
        $this->company_id = $this->row_value('CompanyId');
        $this->company_name = $this->row_value('CompanyName');
        $this->user_id = $this->row_value('UserId');
        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
    }

    public function get_all($start_date, $end_date) {
        if ($this->user_id != null) {
            $this->additional_query = ' WHERE UserId = ' . $this->user_id;
        }

        if ($this->company_id != null) {
            $this->additional_query_empty();

            $this->additional_query .= 'CompanyId = ' . $this->company_id;
        }

        if ($start_date != null && $end_date != null) {
            $this->additional_query_empty();

            $this->additional_query .= 'DateAdded BETWEEN ' . $start_date . ' AND ' . $end_date;
        } else if ($start_date != null || $end_date != null) {
            $this->additional_query_empty();
            $this->additional_query .= 'DateAdded = ';

            if ($start_date != null)  {
                $this->additional_query .= $end_date;
            }

            if ($end_date != null) {
                $this->additional_query .= $end_date;
            }
        }

        $this->stmt = $this->prepare_stmt($this->select_all . $this->additional_query);

        $this->execute();

        return $this->stmt;
    }

    private function clean_data() {
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->company_id = htmlspecialchars(strip_tags($this->company_id));
        $this->miscellaneous_id = htmlspecialchars(strip_tags($this->miscellaneous_id));
    }
}