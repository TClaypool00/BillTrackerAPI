<?php
class All extends BaseClass {
    private $get_bills;
    private $get_loans;
    private $get_subs;
    private $get_misc;
    private $select_all = 'SELECT * FROM ';
    private $where = ' WHERE userId = ';
    private $date_where = ' AND (MONTH(DateAdded) = MONTH(NOW()) AND YEAR(DateAdded) = YEAR(NOW()))';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function get($index) {
        $query = '';
        $this->get_bills = $this->select_all . 'vwbills' . $this->where . $this->user_id;
        $this->get_loans = $this->select_all . 'vwloans' . $this->where . $this->user_id;
        $this->get_subs = $this->select_all . 'wvsubscriptions' . $this->where . $this->user_id;
        $this->get_misc = $this->select_all . 'vwmiscellaneous' . $this->where . $this->user_id . $this->date_where;


        if ($index === 0) {
            $query = $this->get_bills;
        } else if ($index === 1) {
            $query = $this->get_loans;
        } else if ($index === 2) {
            $query = $this->get_subs;
        } else {
            $query = $this->get_misc;
        }

        $this->stmt = $this->prepare_stmt($query);

        $this->execute();

        return $this->stmt;
    }
}