<?php
class Miscellaneous extends BaseClass {
    public $miscellaneous_id;
    public $name;
    public $amount;
    public $date_added;

    public static $not_has_access = 'You do not have access to this Miscellaneous';

    private $select_all = 'SELECT * FROM vwmiscellaneous';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create() {
        $this->clean_data();        

        $this->stmt = $this->prepare_stmt("CALL insMisc('{$this->name}', '{$this->amount}', '{$this->company_id}');");

        $this->execute();

        $this->miscellaneous_id = $this->stmt->fetchColumn();
    }

    public function update() {
        $this->clean_data();
        
        $this->stmt = $this->prepare_stmt("CALL updMisc('{$this->name}', '{$this->amount}', '{$this->company_id}', '{$this->miscellaneous_id}');");
    
        return $this->stmt_executed();
    }

    public function delete() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL delMisc('{$this->miscellaneous_id}');");
    
        return $this->stmt_executed();
    }

    public function get() {
        $this->query = $this->select_all . ' WHERE MiscellaneousId = ' . $this->miscellaneous_id;
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();

        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->miscellaneous_id = $this->row_value('MiscellaneousId');
        $this->name = $this->row_value('Name');
        $this->amount = $this->row_value('Amount');
        $this->date_added = $this->row_value('DateAdded');
        $this->company_id = $this->row_value('CompanyId');
        $this->company_name = $this->row_value('CompanyName');
        $this->user_id = $this->row_value('UserId');
        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
    }

    public function get_all($start_date, $end_date) {
        if ($this->user_id != null) {
            $this->additional_query = ' WHERE UserId = ' . $this->user_id;
        }

        if ($this->company_id != null) {
            $this->additional_query_empty();

            $this->additional_query .= 'CompanyId = ' . $this->company_id;
        }

        if ($start_date != null && $end_date != null) {
            $this->additional_query_empty();

            $this->additional_query .= 'DateAdded BETWEEN ' . $start_date . ' AND ' . $end_date;
        } else if ($start_date != null || $end_date != null) {
            $this->additional_query_empty();
            $this->additional_query .= 'DateAdded = ';

            if ($start_date != null)  {
                $this->additional_query .= $start_date;
            }

            if ($end_date != null) {
                $this->additional_query .= $end_date;
            }
        }

        $this->stmt = $this->prepare_stmt($this->select_all . $this->additional_query);

        $this->execute();

        return $this->stmt;
    }

    public function data_is_null() {
        if (is_null($this->name)) {
            $this->status = 'Name' . $this->cannot_be_null;
        }

        if (is_null($this->amount)) {
            $this->format_status();
            $this->status .= 'Amount' . $this->cannot_be_null;
        }
    }

    public function validate_data() {
        if (!is_null($this->name)) {
            if ($this->name === '') {
                $this->format_status();
                $this->status .= 'Name cannot be an empty string';
            } else {
                $this->name = strval($this->name);
            }
        }

        if (!is_null($this->amount)) {
            if (is_numeric($this->amount)) {
                if ($this->amount <= 0) {
                    $this->format_status();
                    $this->status .= 'Amount must be a positive number';
                } else {
                    $this->amount = doubleval($this->amount);
                }
            } else {
                $this->format_status();
                $this->status .= 'Amount must be a number';
            }
        }
    }

    public function data_is_too_long() {
        if (is_string($this->name) && strlen($this->name) > 255) {
            $this->format_status();
            $this->status .= 'Name' . $this->too_long;
        } 
    }

    public function misc_params_null($start_date, $end_date) {
        if (is_null($this->user_id) && is_null($this->company_id) && is_null($start_date) && is_null($end_date)) {
            return true;
        }

        return false;
    }

    public function user_has_miscellaneous() {
        $this->query = 'SELECT EXISTS(SELECT m.MiscellaneousId FROM miscellaneous m INNER JOIN companies c ON m.CompanyId = c.CompanyId WHERE m.MiscellaneousId = ' . $this->miscellaneous_id . ' AND c.UserId = ' . $this->user_id . ') AS UserHasMiscellaneous';
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();
        return $this->convert_to_boolean($this->stmt->fetchColumn());
    }

    public function miscellaneous_exists() {
        $this->query = 'SELECT EXISTS(SELECT m.MiscellaneousId FROM miscellaneous m WHERE m.MiscellaneousId = ' . $this->miscellaneous_id . ') AS MiscellaneousExists;';
        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();
        return $this->convert_to_boolean($this->stmt->fetchColumn());
    }

    public function miscellaneous_array(bool $include_drop_down = false, bool $include_user_info = false, $message = null, bool $show_currency = false) {
        if ($show_currency) {
            $this->amount = currency($this->amount);
        }

        $misc_arr = array(
            'miscellaneousId' => $this->miscellaneous_id,
            'name' => $this->name,
            'amount' => $this->amount,
            'companyId' => $this->company_id,
            'companyName' => $this->company_name
        );

        if ($include_drop_down) {
            $misc_arr['companies'] = $this->drop_down();
        }

        if ($include_user_info) {
            $misc_arr['userId'] = $this->user_id;
            $misc_arr['firstName'] = $this->user_first_name;
            $misc_arr['lastName'] = $this->user_last_name;
        }

        if ($message !== null) {
            $misc_arr['message'] = $message;
        }

        return json_encode($misc_arr);
    }

    private function clean_data() {
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->company_id = htmlspecialchars(strip_tags($this->company_id));
        $this->miscellaneous_id = htmlspecialchars(strip_tags($this->miscellaneous_id));
    }
}