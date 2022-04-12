<?php
class Bill extends BaseClass {
    public $bill_id;
    public $bill_name;
    public $amount_due;

    public function __construct($db)
    {
        $this->conn = $db;
    }
}