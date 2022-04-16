<?php
class Bill extends BaseClass {
    public $bill_id;
    public $bill_name;
    public $amount_due;
    public $is_recurring;
    public $is_active;
    public $end_date;

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

    private function clean_data() {
        $this->bill_name = htmlspecialchars(strip_tags($this->bill_name));
        $this->amount_due = htmlentities(strip_tags($this->amount_due));
        $this->is_recurring = htmlspecialchars(strip_tags($this->is_recurring));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
    }
}