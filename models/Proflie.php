<?php
class Proflie extends BaseClass {
    public $profile_id;
    public $monthly_salary;
    public $budget;
    public $savings;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function update() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL updateProfile('{$this->profile_id}');");

        return $this->stmt_executed();
    }

    public function user_has_profile() {
        $this->query = 'SELECT EXISTS(SELECT ProfileId FROM userprofile WHERE ProfileId = ' . $this->profile_id . ' AND UserId = ' . $this->user_id . ') AS UserHasProfile';

        $this->stmt = $this->prepare_stmt($this->query);

        $this->execute();
        return $this->convert_to_boolean($this->stmt->fetchColumn());
    }

    public function profile_exists() {
        $this->query = 'SELECT EXISTS(SELECT ProfileId FROM userprofile WHERE ProfileId = ' . $this->profile_id . ') AS ProfileExists';
        
        $this->stmt = $this->prepare_stmt($this->query);

        $this->execute();
        return $this->convert_to_boolean($this->stmt->fetchColumn());
    }

    public function data_is_null() {
        if (is_null($this->monthly_salary)) {
            $this-> status = 'Monthly salary' . $this->cannot_be_null;
        }

        if (is_null($this->budget)) {
            $this->format_status();
            $this->status .= 'Budget' . $this->cannot_be_null;
        }

        if (is_null($this->savings)) {
            $this->format_status();
            $this->status .= 'Savings' . $this->cannot_be_null;
        }
    }

    public function format_data() {
        if (!is_null($this->monthly_salary)) {
            if (is_numeric($this->monthly_salary)) {
                $this->monthly_salary = doubleval($this->monthly_salary);
            } else {
                $this->format_status();
                $this->status .= 'Monthly salary must be a number';
            }
        }

        if (!is_null($this->budget)) {
            if (is_numeric($this->budget)) {
                $this->budget = doubleval($this->budget);
            } else {
                $this->format_status();
                $this->status .= 'Budget must be a nubmer';
            }
        }

        if (!is_null($this->savings)) {
            if (is_numeric($this->savings)) {
                $this->savings = doubleval($this->savings);
            } else {
                $this->format_status();
                $this->status .= 'Savings must be a nubmer';
            }
        }
    }

    public function validate_data() {
        if (doubleval($this->monthly_salary)) {
            if ($this->monthly_salary <= 0) {
                $this->format_status();
                $this->status .= 'Monthly salary must be a positive number';
            }
        }

        if (doubleval($this->budget)) {
            if ($this->budget <= 0) {
                $this->format_status();
                $this->status .= 'Budget must be a positive number';
            }
        }

        if (doubleval($this->savings)) {
            if ($this->savings <= 0) {
                $this->format_status();
                $this->status .= 'Savings must be a positive number';
            }
        }
    }

    private function clean_data() {
        $this->monthly_salary = htmlspecialchars(strip_tags($this->monthly_salary));
        $this->budget = htmlspecialchars(strip_tags($this->budget));
        $this->savings = htmlspecialchars(strip_tags($this->savings));
    }
}