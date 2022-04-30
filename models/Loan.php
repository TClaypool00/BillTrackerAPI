<?php
class Loan extends BaseClass {
    public $loan_id;
    public $loan_name;
    public $monthly_amt_due;
    public $total_loan_amt;
    public $remaining_amt;

    private $select_all = 'SELECT * FROM vwloans';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insLoan('{$this->loan_name}', '{$this->monthly_amt_due}', '{$this->total_loan_amt}', '{$this->remaining_amt}', '{$this->user_id}');");

        return $this->stmt_executed();
    }
    
    public function update()
    {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL updLoan('{$this->loan_name}', '{$this->is_active}', '{$this->monthly_amt_due}', '{$this->total_loan_amt}', '{$this->remaining_amt}', '{$this->loan_id}');");

        return $this->stmt_executed();
    }

    public function get() {
        $this->query = $this->select_all . ' WHERE  LoanId = ' . $this->loan_id . $this->limit;
        
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->loan_name = $this->row_value('LoanName');
        $this->is_active = $this->row_value('IsActive');
        $this->monthly_amt_due = $this->row_value('MonthlyAmountDue');
        $this->total_loan_amt = $this->row_value('TotalAmountDue');
        $this->remaining_amt = $this->row_value('RemainingAmount');
        $this->user_id = $this->row_value('UserId');
        $this->company_id = $this->row_value('CompanyId');
        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
        $this->company_name = $this->row_value('CompanyName');
    }

    public function get_all($by_user, $by_company, $by_active) {
        if ($by_user) {
            $this->additional_query = ' WHERE UserId = ' . $this->user_id;
        }

        if ($by_company) {
            $this->additional_query_empty();

            $this->additional_query .= ' CompanyId = ' . $this->company_id;
        }

        if ($by_active) {
            $this->additional_query_empty();

            $this->additional_query = ' IsActive = ' . $this->is_active;
        }

        $this->stmt = $this->prepare_stmt($this->select_all . $this->additional_query);

        $this->execute();

        return $this->stmt;
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