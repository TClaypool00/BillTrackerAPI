<?php
class Subscription extends BaseClass
{
    public $subscription_id;
    public $name;
    public $amount_due;

    public static $not_access = 'You do have access to this Subscription';

    private $select_all = 'SELECT * FROM vwsubscriptions';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL insSub('{$this->name}', '{$this->amount_due}', '{$this->date_due}', '{$this->company_id}');");
        $this->execute();

        $this->subscription_id = $this->stmt->fetchColumn();
    }

    public function update()
    {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL updSub('{$this->name}', '{$this->amount_due}', '{$this->date_due}', '{$this->is_active}', '{$this->company_id}', '{$this->subscription_id}');");

        return $this->stmt_executed();
    }

    public function get()
    {
        $this->query = $this->select_all . ' WHERE SubscriptionId = ' . $this->subscription_id;
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->name = $this->row_value('Name');
        $this->amount_due = $this->row_value('AmountDue');

        $this->base_get();
    }

    public function get_all()
    {
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

        if ($this->is_active !== null) {
            $this->additional_query_empty();
            $this->additional_query .= 'IsActive = ' . $this->due_date;
        }

        if ($this->is_late !== null) {
            $this->additional_query_empty();
            $this->additional_query .= 'IsLate = ' . $this->due_date;
        }

        if ($this->is_paid !== null) {
            $this->additional_query_empty();
            $this->additional_query .= 'IsPaid = ' . $this->due_date;
        }

        if (!is_null($this->search)) {
            $this->additional_query_empty();
            $this->additional_query .= 'Name LIKE %' . $this->search . '% OR AmountDue LIKE %' . $this->search . '%';
        }

        $this->limit_by_index();

        $this->stmt = $this->prepare_stmt($this->select_all . $this->additional_query);

        $this->execute();

        return $this->stmt;
    }

    public function sub_exists()
    {
        $this->query = 'SELECT EXISTS(SELECT SubscriptionId FROM subscriptions WHERE SubscriptionId = ' . $this->subscription_id .  ') AS SubExists;';

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return $this->convert_to_boolean($this->stmt->fetchColumn());
    }

    public function user_has_sub() {
        $this->query = 'SELECT EXISTS(SELECT s.SubscriptionId FROM subscriptions s INNER JOIN companies c ON s.CompanyId = c.CompanyId WHERE s.SubscriptionId = ' . $this->subscription_id . ' AND c.UserId = ' . $this->user_id . ') AS UserHasSub';
    
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        return $this->convert_to_boolean($this->stmt->fetchColumn());
    }

    public function data_is_null() {
        if ($this->name === null) {
            $this->status = 'Name' . $this->cannot_be_null;
        }

        if ($this->amount_due === null) {
            $this->format_status();
            $this->status .= 'Amount due' . $this->cannot_be_null;
        }
    }

    public function validate_data() {
        if (!is_null($this->name)) {
            if (is_string($this->name)) {
                $this->name = strval($this->name);
            } else {
                $this->format_status();
                $this->status .= 'Name must be a string';
            }
        }

        if (!is_null($this->amount_due)) {
            if (is_numeric($this->amount_due)) {
                $this->amount_due = doubleval($this->amount_due);
            } else {
                $this->format_status();
                $this->status .= 'Amount due maust be number';
            }
        }
    }

    public function data_too_small() {
        if (is_string($this->name) && $this->name === '') {
            $this->format_status();
            $this->status .= 'Name cannot be an empty string';
        }

        if (is_double($this->amount_due) && $this->amount_due <= 0) {
            $this->format_status();
            $this->status .= 'Amount due must be a positive number';
        }
    }

    public function data_too_long() {
        if (is_string($this->name) && strlen($this->name) > 255) {
            $this->format_status();
            $this->status .= 'Name' . $this->too_long;
        }
    }

    public function sub_array(bool $inclue_drop_down = false, bool $inclue_user = false, $message = null) {
        $this->date_due = $this->format_date($this->date_due);

        $sub_arr = array(
            'subscriptionId' => $this->subscription_id,
            'name' => $this->name,
            'amountDue' => $this->amount_due,
            'dueDate' => $this->date_due,
            'isActive' => boolval($this->is_active),
            'datePaid' => $this->date_paid,
            'isPaid' => boolval($this->is_paid),
            'isLate' => boolval($this->is_late),
            'companyId' => $this->company_id
        );

        $sub_arr['companyName'] = $this->company_name;

        if ($message !== null) {
            $sub_arr['message'] = $message;
        }
        
        if ($inclue_drop_down) {
            $sub_arr['companies'] = $this->drop_down();
        }

        if ($inclue_user) {
            $sub_arr['userId'] = $this->user_id;
            $sub_arr['firstName'] = $this->user_first_name;
            $sub_arr['lastName'] = $this->user_last_name;
        }

        return json_encode($sub_arr);
    }

    private function clean_data()
    {
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->amount_due = htmlspecialchars(strip_tags($this->amount_due));
        $this->date_due = htmlspecialchars(strip_tags($this->date_due));
        $this->company_id = htmlspecialchars(strip_tags($this->company_id));
        $this->is_active = htmlspecialchars(strip_tags($this->is_active));
    }
}
