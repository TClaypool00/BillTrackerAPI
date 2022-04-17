<?php
class Loan extends BaseClass {
    public $loan_id;
    public $loan_name;
    public $monthly_amt_due;
    public $total_loan_amt;
    public $remaining_amt;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insLoan('{$this->loan_name}', '{$this->monthly_amt_due}', '{$this->total_loan_amt}', '{$this->remaining_amt}', '{$this->user_id}');");

        return $this->stmt_executed();
    }

    private function clean_data() {
        $this->loan_name = htmlspecialchars(strip_tags($this->loan_name));
        $this->is_active = htmlspecialchars(strip_tags($this->is_active));
        $this->monthly_amt_due = htmlspecialchars(strip_tags($this->monthly_amt_due));
        $this->total_loan_amt = htmlspecialchars(strip_tags($this->total_loan_amt));
        $this->remaining_amt = htmlspecialchars(strip_tags($this->remaining_amt));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
    }
}