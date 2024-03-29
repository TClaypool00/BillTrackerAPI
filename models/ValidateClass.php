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
    public $date_paid;
    public $is_late;
    public $is_paid;
    public $show_currency;
    public $is_edit;
    public $is_edited;
    public $return_object;
    public $include_drop_down;
    public $search;
    public $comment_id;
    public $parent_id;
    public $index;
    public $date_added;
    public $date_posted;
    public $start_date;
    public $end_date;

    public $status = '';
    public static $all_params_null = 'Only admins can have all parameters be null';
    public static $not_auth = 'Not authorized';
    public static $does_not_have_company = 'You do not have access to this company';
    public static $user_id_null = 'User Id cannot be null';
    public static $is_edit_show_currency = 'Both is edit and show currency cannot both be true';
    
    protected $cannot_empty = ' cannot not be empty';
    protected $too_long = ' can only be a maxiumn of 255 characters';
    protected $cannot_be_null = ' cannot be null';
    protected $must_be_num = ' must be a number';
    protected $not_found = ' not found';
    protected $user_id_string = 'User Id';
    protected $not_an_option = 'Not a valid option';

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
            case BooleanTypes::ShowCurrency:
                $value = $this->show_currency;
                $value_name ='Show currency';
                break;
            case BooleanTypes::IsEdit:
                $value = $this->is_edit;
                $value_name = 'IsEdit';
                break;
            case BooleanTypes::IsEdited:
                $value = $this->is_edited;
                $value_name = 'IsEdited';
                break;
            case BooleanTypes::ReturnObject:
                $value = $this->return_object;
                $value_name = 'ReturnObject';
                break;
            case BooleanTypes::IncludeDropDown:
                $value = $this->include_drop_down;
                $value_name = 'IncludeDropDown';
                break;
            default:
                throw new TypeError($this->not_an_option);
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
                    $this->status .= $value_name . ' is not a valid string';
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
                break;
            case BooleanTypes::ShowCurrency:
                $this->show_currency = $value;
                break;
            case BooleanTypes::IsEdit:
                $this->is_edit = $value;
                break;
            case BooleanTypes::IsEdited:
                $this->is_edited = $value;
                break;
            case BooleanTypes::ReturnObject:
                $this->return_object = $value;
                break;
            case BooleanTypes::IncludeDropDown:
                $this->include_drop_down = $value;
                break;
            default:
            throw new TypeError($this->not_an_option);
        }
    }

    public function validate_id(IdTypes $type, bool $can_be_null = false) {
        $value = null;
        $value_name = '';

        switch($type) {
            case IdTypes::UserId:
                $value = $this->user_id;
                $value_name = 'UserId';
                break;
            case IdTypes::CommentId:
                $value = $this->comment_id;
                $value_name = 'CommentId';
                break;
            case IdTypes::ParentId:
                $value = $this->parent_id;
                $value_name = 'ParentId';
                break;
            case IdTypes::TypeId:
                $value = $this->type_id;
                $value_name = 'TypeId';
                break;
            case IdTypes::CompanyId:
                $value = $this->company_id;
                $value_name = 'CompanyId';
                break;
            case IdTypes::Index:
                $value = $this->index;
                $value_name = 'Index';
                break;
            default:
            throw new TypeError($this->not_an_option);
        }

        if ($value === null) {
            if (!$can_be_null) {
                $this->format_status();
                $this->status .= $value_name . $this->cannot_be_null;
            }
        } else {
            if (is_numeric($value)) {
                if ($value < 0) {
                    $this->format_status();
                    $this->status .= $value_name . ' must be greater than 0';
                } else {
                    $value = intval($value);
                }
            } else {
                $this->format_status();
                $this->status .= $value_name . $this->must_be_num;
            }
        }

        switch($type) {
            case IdTypes::UserId:
                $this->user_id = $value;
                break;
            case IdTypes::CommentId:
                $this->comment_id = $value;
                break;
            case IdTypes::ParentId:
                $this->parent_id = $value;
                break;
            case IdTypes::TypeId:
                $this->type_id = $value;
                break;
            case IdTypes::CompanyId:
                $this->company_id = $value;
                break;
            case IdTypes::Index:
                $this->index = $value;
                break;
            default:
            throw new TypeError($this->not_an_option);
        }
    }

    public function is_date_null() {
        if (is_null($this->date_due)) {
            $this->format_status();
            $this->status .= 'Due date' . $this->cannot_be_null;
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

    protected function first_index_string() {
        $this->user_last_name = substr($this->user_last_name, 0, 1) . '.';
    }

    public function validate_date(DateType $date, $can_be_null = false, $can_be_in_past = false) {
        $value = null;
        $value_name = '';

        switch($date) {
            case DateType::DateDue:
                $value = $this->date_due;
                $value_name = 'Date due';
                break;
            case DateType::DateAdded:
                $value = $this->date_added;
                $value_name = 'Date added';
                break;
            case DateType::DatePosted:
                $value = $this->date_posted;
                $value_name = 'Date posted';
                break;
            case DateType::StartDate:
                $value = $this->start_date;
                $value_name = 'Start date';
                break;
            case DateType::EndDate:
                $value = $this->end_date;
                $value_name = 'End date';
                break;
            default:
                throw new TypeError($this->not_an_option);
        }

        if ($value === null) {
            if (!$can_be_null) {
                $this->format_status();
                $this->status .= $value_name . ' cannot be null';
            }
        } else if($this->is_date($value)) {
            $this->create_time_stamp($value);
            $value = $this->convert_string_to_date();
            if (!$can_be_in_past && strtotime(date('Y-m-d', strtotime($value))) < strtotime(date('Y-m-01'))) {
                $this->format_status();
                $this->status .= $value_name . ' cannot be in the past';
            }
        } else {
            $this->format_status();
            $this->status .= $value_name . ' is not a valid date';
        }

        switch($date) {
            case DateType::DateDue:
                $this->date_due = $value;
                break;
            case DateType::DateAdded:
                $this->date_added = $value;
                break;
            case DateType::DatePosted:
                $this->date_posted = $value;
                break;
            case DateType::StartDate:
                $this->start_date = $value;
                break;
            case DateType::EndDate:
                $this->end_date = $value;
                break;
            default:
                throw new TypeError($this->not_an_option);
        }
    }

    public function status_is_empty() {
        return $this->status === '';
    }
}