<?php
class Subscription extends BaseClass {
    public $subscription_id;
    public $name;
    public $amount_due;

    private $select_all = 'SELECT * FROM wvsubscriptions';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insSub('{$this->name}', '{$this->amount_due}', '{$this->date_due}', '{$this->company_id}', '{$this->due_date}');");

        return $this->stmt_executed();
    }

    public function update() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL updSub('{$this->name}', '{$this->amount_due}', '{$this->date_due}', '{$this->is_active}', '{$this->company_id}', '{$this->subscription_id}');");

        return $this->stmt_executed();
    }

    public function get() {
        $this->query = $this->select_all . ' WHERE SubscriptionId = ' . $this->subscription_id;
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $this->row_value('Name');
        $this->amount_due = $this->row_value('AmountDue');
        $this->due_date = $this->row_value('DueDate');
        $this->is_active = $this->row_value('IsActive');
        $this->company_id = $this->row_value('CompanyId');
        $this->company_name = $this->row_value('CompanyName');
        $this->user_id = $this->row_value('UserId');
        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
    }

    public function get_all() {
        if ($this->user_id != null) {
            $this->additional_query = ' WHERE UserId = ' . $this->user_id;
        }

        if ($this->company_id != null) {
            $this->additional_query_empty();

            $this->additional_query .= 'CompanyId = ' . $this->company_id;
        }

        if ($this->due_date != null) {
            $this->additional_query_empty();

            $this->additional_query .= 'DueDate = ' . $this->due_date;
        }

        $this->stmt = $this->prepare_stmt($this->select_all . $this->additional_query);

        $this->execute();

        return $this->stmt;
    }

    public function sub_exists() {
        $this->query = 'SELECT EXISTS(SELECT * FROM subscriptions WHERE SubscriptionId = ' . $this->subscription_id .  ') AS SubExists;';

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return $this->convert_to_boolean($this->stmt->fetchColumn());
    }

    public function format_data() {
        $this->name = strval($this->name);
        if (!is_numeric($this->amount_due)) {
            $this->format_status();
            $this->status .= 'Amount due must be a number';
        } else {
            $this->amount_due = doubleval($this->amount_due);
        }

        if (!is_null($this->company_id)) {
            $this->company_id = intval($this->company_id);
        } else {
            $this->format_status();
            $this->status .= 'Company Id cannot be be null';
        }
    }

    public function validate_data() {
        if ((strlen($this->name) === 0) || $this->name === null) {
            $this->format_status();
            $this->status .= 'Name' . $this->cannot_empty;
        }

        if ($this->amount_due <= 0) {
            $this->format_status();
            $this->status .= 'Amount due must be a positive number';
        }

        if ($this->company_id <= 0 && is_numeric($this->company_id)) {
            $this->format_status();
            $this->status .= 'Company Id must be a postive number';
        }
    }

    private function clean_data() {
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->amount_due = htmlspecialchars(strip_tags($this->amount_due));
        $this->date_due = htmlspecialchars(strip_tags($this->date_due));
        $this->company_id = htmlspecialchars(strip_tags($this->company_id));
        $this->is_active = htmlspecialchars(strip_tags($this->is_active));
    }
}