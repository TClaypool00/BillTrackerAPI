<?php
class Bill extends BaseClass {
    public $bill_id;
    public $bill_name;
    public $amount_due;
    public $is_recurring;
    public $is_active;
    public $end_date;
    public $amount_due_curr;

    private $select_all = 'SELECT * FROM vwbills';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insBill('{$this->bill_name}', '{$this->amount_due}', '{$this->user_id}', '{$this->is_recurring}', '{$this->end_date}');");

        return $this->stmt_executed();
    }

    public function update() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL updBill('{$this->bill_name}', '{$this->amount_due}', '{$this->is_recurring}', '{$this->is_active}', '{$this->end_date}', '{$this->bill_id}');");

        return $this->stmt_executed();
    }

    public function get() {
        $this->query = $this->select_all . ' WHERE BillId =' . $this->bill_id;

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->bill_name = $this->row_value('BillName');
        $this->amount_due = $this->row_value('AmountDue');
        $this->is_recurring = $this->row_value('IsRecurring');
        $this->is_active = $this->row_value('IsActive');
        $this->end_date = $this->row_value('EndDate');
        $this->user_id = $this->row_value('UserId');
        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');

        $this->amount_due_curr = $this->currency($this->amount_due);
    }

    private function clean_data() {
        $this->bill_name = htmlspecialchars(strip_tags($this->bill_name));
        $this->amount_due = htmlentities(strip_tags($this->amount_due));
        $this->is_recurring = htmlspecialchars(strip_tags($this->is_recurring));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
    }
}