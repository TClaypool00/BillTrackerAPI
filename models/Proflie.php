<?php
class Proflie extends BaseClass {
    public $monthly_salary;
    public $budget;
    public $savings;

    public function __construct($db)
    {
        $this->conn = $db;
    }
}