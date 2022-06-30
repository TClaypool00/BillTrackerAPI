<?php
class Bill extends BaseClass
{
    public $bill_id;
    public $bill_name;
    public $amount_due;
    public $amount_due_curr;
    public $not_access_bill = 'You do have have access to this bill';

    private $select_all = 'SELECT * FROM vwbills';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insBill('{$this->bill_name}', '{$this->amount_due}', '{$this->company_id}', '{$this->date_due}');");

        return $this->stmt_executed();
    }

    public function update()
    {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL updBill('{$this->bill_name}', '{$this->amount_due}', '{$this->is_active}', '{$this->bill_id}');");

        return $this->stmt_executed();
    }

    public function get()
    {
        $this->query = $this->select_all . ' WHERE BillId =' . $this->bill_id;

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->bill_name = $this->row_value('BillName');
        $this->amount_due = $this->row_value('AmountDue');
        $this->is_active = $this->row_value('IsActive');
        $this->user_id = $this->row_value('UserId');
        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
    }

    public function get_all()
    {
        $this->query = $this->select_all;

        if ($this->user_id !== null) {
            $this->additional_query = ' WHERE UserId = ' . $this->user_id;
        }

        if ($this->is_active !== null) {
            $this->additional_query_empty();

            $this->additional_query .= 'IsActive = ' . $this->is_active;
        }

        if ($this->is_paid !== null) {
            $this->additional_query_empty();
            $this->additional_query .= 'IsPaid =' . $this->is_paid;
        }

        if ($this->is_late !== null) {
            $this->additional_query_empty();
            $this->additional_query .= 'IsLate =' . $this->is_late;
        }

        $this->stmt = $this->prepare_stmt($this->query . $this->additional_query);

        $this->execute();

        return $this->stmt;
    }

    public function bill_exists()
    {
        $this->query = 'SELECT EXISTS(SELECT BillId FROM bills WHERE BillId = ' . $this->bill_id .  ') AS BillExists;';

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return $this->stmt->fetchColumn();
    }

    public function user_has_bill() {
        $this->query = 'SELECT EXISTS (SELECT BillId from bills b INNER JOIN companies c ON c.CompanyId = b.CompanyId WHERE c.UserId = ' . $this->user_id . ' AND b.BillId = ' . $this->bill_id . ') AS UserHasBill';
    
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return $this->stmt->fetchColumn();
    }

    public function data_is_null() {
        if (is_null($this->bill_name)) {
            $this->status .= 'Bill name' . $this->cannot_be_null;
        }

        if (is_null($this->amount_due)) {
            $this->format_status();
            $this->status .= 'Amount due ' . $this->cannot_be_null;
        }
    }

    public function validate_bill_name() {
        if (!is_null($this->bill_name)){
            if (is_string($this->bill_name)) {
                if ($this->bill_name !== '') {
                    $this->bill_name = strval($this->bill_name);
                } else {
                    $this->format_status();
                    $this->status .= 'Bill name ' . $this->cannot_empty;
                }
            } else {
                $this->format_status();
                $this->status .= 'Bill name must be a string';
            }
        }
    }

    public function validate_amount_due() {
        if(!is_null($this->amount_due)) {
            if (is_numeric($this->amount_due)) {
                if ($this->amount_due > 0) {
                    $this->amount_due = intval($this->amount_due);
                } else {
                    $this->format_status();
                    $this->status .= '';
                }
            } else {
                $this->format_status();
                $this->status .= 'Amount due must be a number';
            }
        }
    }

    private function clean_data()
    {
        $this->bill_name = htmlspecialchars(strip_tags($this->bill_name));
        $this->amount_due = htmlentities(strip_tags($this->amount_due));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->company_id = htmlspecialchars(strip_tags($this->company_id));
        $this->is_active = htmlspecialchars(strip_tags($this->is_active));
        $this->date_due = htmlspecialchars(strip_tags($this->date_due));
    }
}
