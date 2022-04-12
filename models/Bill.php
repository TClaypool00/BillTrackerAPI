<?php
class Bill extends BaseClass {
    public $bill_id;
    public $bill_name;
    public $amount_due;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insUser('{$this->bill_name}', '{$this->amount_due}', '{$this->user_id}');");

        return $this->stmt_executed();
    }

    private function clean_data() {
        $this->bill_name = htmlspecialchars(strip_tags($this->bill_name));
        $this->amount_due = htmlentities(strip_tags($this->amount_due));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
    }
}