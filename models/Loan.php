<?php
class Loan extends BaseClass {
    public $loan_id;
    public $loan_name;
    public $monthly_amt_due;
    public $total_loan_amt;
    public $remaining_amt;
    public $loan_not_found;

    private $select_all = 'SELECT * FROM vwloans';

    public function __construct($db)
    {
        $this->conn = $db;
        $this->loan_not_found = 'Loan' . $this->not_found;

    }

    public function create() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insLoan('{$this->loan_name}', '{$this->monthly_amt_due}', '{$this->total_loan_amt}', '{$this->remaining_amt}', '{$this->company_id}', '{$this->date_due}');");

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

    public function get_all() {
        if ($this->user_id !== null) {
            $this->additional_query = ' WHERE UserId = ' . $this->user_id;
        }

        if ($this->company_id !== null) {
            $this->additional_query_empty();
            $this->additional_query .= ' CompanyId = ' . $this->company_id;
        }

        if ($this->is_active !== null) {
            $this->additional_query_empty();
            $this->additional_query .= ' IsActive = ' . $this->is_active;
        }

        $this->stmt = $this->prepare_stmt($this->select_all . $this->additional_query);

        $this->execute();

        return $this->stmt;
    }

    public function loan_exists() {
        $this->query = 'SELECT EXISTS(SELECT * FROM loans WHERE LoanId = ' . $this->loan_id .  ') AS LoanExists;';

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return $this->stmt->fetchColumn();
    }

    public function data_is_null() {
        if (is_null($this->loan_name)) {
            $this->format_status();
            $this->status .= 'Loan name' . $this->cannot_be_null;
        } else {
            $this->loan_name = strval($this->loan_name);
        }

        if (is_null($this->monthly_amt_due) || $this->monthly_amt_due === 0) {
            $this->format_status();
            $this->status .= 'Amount due' . $this->cannot_be_null . ' or 0';
        }

        if (is_null($this->total_loan_amt) || $this->total_loan_amt === 0) {
            $this->format_status();
            $this->status .= 'Total amount due' . $this->cannot_be_null . ' or 0';
        }
    }

    public function format_data() {
        if (!is_null($this->monthly_amt_due) && is_numeric($this->monthly_amt_due)){
            $this->monthly_amt_due = doubleval($this->monthly_amt_due);
        } else {
            $this->format_status();
            $this->status .= 'Monthly amount' . $this->must_be_num;
        }

        if (!is_null($this->total_loan_amt) && is_numeric($this->total_loan_amt)){
            $this->total_loan_amt = doubleval($this->monthly_amt_due);
        } else {
            $this->format_status();
            $this->status .= 'Total amount' . $this->must_be_num;
        }
        
        $this->format_remaing_amount();
    }

    public function validate_amount() {
        if ($this->remaining_amt >  $this->total_loan_amt) {
            $this->format_status();
            $this->status .= 'Remaining amount cannot be more than total amount';
        }

        if ($this->monthly_amt_due > $this->total_loan_amt) {
            $this->format_status();
            $this->status .= 'Monthly amount due cannot be more than total amount';
        }
    }

    public function user_has_loan() {
        $this->query = 'SELECT EXISTS(SELECT l.LoanId FROM loans l INNER JOIN companies c ON l.CompanyId = c.CompanyId WHERE l.LoanId = ' . $this->loan_id .  ' AND c.UserId = ' . $this->user_id . ') AS UserHasLoans;';

        $this->stmt = $this->prepare_stmt($this->query);

        $this->execute();
        return $this->convert_to_boolean($this->stmt->fetchColumn());
    }

    private function clean_data() {
        $this->loan_name = htmlspecialchars(strip_tags($this->loan_name));
        $this->is_active = htmlspecialchars(strip_tags($this->is_active));
        $this->monthly_amt_due = htmlspecialchars(strip_tags($this->monthly_amt_due));
        $this->total_loan_amt = htmlspecialchars(strip_tags($this->total_loan_amt));
        $this->remaining_amt = htmlspecialchars(strip_tags($this->remaining_amt));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
    }
    
    private function format_remaing_amount() {
        if (is_null($this->remaining_amt)) {
            if (is_numeric($this->total_loan_amt)) {
                $this->remaining_amt = $this->total_loan_amt;
            }
        } else {
            if (is_numeric($this->remaining_amt)) {
                $this->remaining_amt = doubleval($this->remaining_amt);
            } else {
                $this->format_status();
                $this->status .= 'Remaining amount must a number';
            }
        }
    }
}