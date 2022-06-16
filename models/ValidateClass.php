<?php
class ValidateClass {
    public $user_id;
    public $user_first_name;
    public $user_last_name;

    public $is_active;

    public $company_id;
    public $company_name;

    public $type_id;
    public $type_name;

    public $date_due;

    public $status = '';
    
    protected $cannot_empty = ' cannot not be empty';
    protected $too_long = ' can only be a maxiumn of 255 characters';
    protected $cannot_be_null = ' cannot be null';
    protected $must_be_num = ' must be a number';
    protected $not_found = ' not found';

    public function validate_is_active() {
        if (!is_bool($this->is_active)) {
            $this->format_status();
            $this->status .= 'Is active has to be a boolean';
        } else{
            $this->is_active = boolval($this->is_active);
        }
    }

    public function validate_company_id() {
        if ($this->company_id === null) {
            $this->format_status();
            $this->status .= 'Company id' . $this->cannot_be_null;
        } else {
            if (is_numeric($this->company_id)) {
                $this->company_id = intval($this->company_id);
            } else {
                $this->format_status();
                $this->status .= 'Company Id' . $this->must_be_num;
            }
        }
    }

    public function is_date_null() {
        if (is_null($this->date_due)) {
            $this->format_status();
            $this->status .= 'Due date' . $this->cannot_be_null;
        }
    }

    public function validate_user_id() {
        if ($this->user_id === null) {
            $this->format_status();
            $this->status .= 'User id' . $this->cannot_be_null;
        } else {
            if (is_numeric($this->user_id)) {
                $this->user_id = intval($this->user_id);
            } else {
                $this->format_status();
                $this->status .= 'User Id' . $this->must_be_num;
            }
        }
    }

    protected function format_status() {
        if ($this->status !== '') {
            $this->status .= ' and ';
        }
    }

    protected function is_date($date_as_string) {
        if (!is_null($date_as_string)) {
            if (DateTime::createFromFormat('Y-m-d', $date_as_string) !== false) {
                return true;
            } else {
                $this->format_status();
                $this->status .= '"' . $date_as_string . '" is not a valid date';
                return false;
            }
        } else {
            return false;
        }
    }

    protected function create_time_stamp($date_as_string) {
        $this->time_stamp = strtotime($date_as_string);
    }

    protected function convert_string_to_date() {
        return date('Y-m-d', $this->time_stamp);
    } 

    public function validate_date() {
        if ($this->date_due === null) {
            $this->format_status();
            $this->status .= 'Date due cannot be null';
        } else if($this->is_date($this->date_due)) {
            $this->create_time_stamp($this->date_due);
            $this->date_due = $this->convert_string_to_date();
        } else {
            $this->format_status();
            $this->status .= 'Date due is not a valid date';
        }
    }
}