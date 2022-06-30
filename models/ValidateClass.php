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
    public $is_late;
    public $is_paid;
    public $is_currency;

    public $status = '';
    public static $all_params_null = 'Only admins can have all parameters be null';
    public static $not_auth = 'Not authorized';
    public static $does_not_have_company = 'You do not have access to this company';
    
    protected $cannot_empty = ' cannot not be empty';
    protected $too_long = ' can only be a maxiumn of 255 characters';
    protected $cannot_be_null = ' cannot be null';
    protected $must_be_num = ' must be a number';
    protected $not_found = ' not found';
    protected $user_id_string = 'User Id';

    public function validate_is_active($can_be_null = false) {
        if ($this->is_active === null) {
            if (!$can_be_null) {
                $this->format_status();
                $this->status .= 'Is Active' . $this->cannot_be_null;
            }
        } else {
            if(is_string($this->is_active)){
                if ($this->is_active === 'true') {
                    $this->is_active = true;
                } else if($this->is_active === 'false') {
                    $this->is_active = 0;
                } else {
                    $this->format_status();
                    $this->status .= 'Not a valid option';
                }
            } else if (is_bool($this->is_active)){
                $this->is_active = boolval($this->is_active);
            } else {
                $this->format_status();
                $this->status .= 'Is Active must be a boolean';
            }
        }
    }

    public function validate_boolean(BooleanTypes $type, $can_be_null = false) {
        $value = null;
        $value_name = '';

        switch ($type) {
            case BooleanTypes::IsPaid:
                $value = $this->is_paid;
                $value_name = 'Is Paid';
                break;
            case BooleanTypes::IsLate:
                $value = $this->is_late;
                $value_name = 'Is Late';
                break;
            case BooleanTypes::IsCurrency:
                $value = $this->is_currency;
                $value_name ='Is currency';
                break;
            default:
                throw new Exception('Not a valid option');
                break;
        }

        if ($value === null) {
            if (!$can_be_null) {
                $this->format_status();
                $this->status .= $value_name . $this->cannot_be_null;
            }
        } else {
            if(is_string($value)){
                if ($value === 'true') {
                    $value = true;
                } else if($value === 'false') {
                    $value = 0;
                } else {
                    $this->format_status();
                    $this->status .= 'Not a valid option';
                }
            } else if (is_bool($value)){
                $value = boolval($value);
            } else {
                $this->format_status();
                $this->status .= $value_name . ' must be a boolean';
            }
        }

        switch ($type) {
            case BooleanTypes::IsPaid:
                $this->is_paid = $value;
                break;
            case BooleanTypes::IsLate:
                $this->is_late = $value;
            case BooleanTypes::IsCurrency:
                $this->is_currency = $value;
            default:
                throw new Exception('Not a valid option');
                break;
        }
    }

    public function validate_company_id($can_be_null = false) {
        if ($this->company_id === null) {
            if (!$can_be_null) {
                $this->format_status();
                $this->status .= 'Company Id' . $this->cannot_be_null;
            }
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

    public function validate_user_id($can_be_null = false) {
        if ($this->user_id === null) {
            if (!$can_be_null) {
                $this->format_status();
                $this->status .= $this->user_id_string . $this->cannot_be_null;
            }
        } else {
            if (is_numeric($this->user_id)) {
                $this->user_id = intval($this->user_id);
            } else {
                $this->format_status();
                $this->status .= $this->user_id_string . $this->must_be_num;
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

    public function status_is_empty() {
        if ($this->status === '') {
            return true;
        }

        return false;
    }
}