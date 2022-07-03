<?php
class User extends BaseClass {
    private $options = [
        'cost' => 11
    ];

    private $select_all = 'SELECT * FROM vwusers';

    public $email;
    public $password;
    public $confirm_password;
    public $isAdmin;
    public $phone_num;
    public $token;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create() {
        $this->clean_data();

        $this->password = $this->encrypt_password();

        $this->stmt = $this->prepare_stmt("CALL insUser('{$this->user_first_name}', '{$this->user_last_name}', '{$this->email}', '{$this->password}', '{$this->phone_num}');");

        return $this->stmt_executed();
    }

    public function get_all() {
        $this->stmt = $this->prepare_stmt($this->select_all);
        $this->execute();

        return $this->stmt;
    }

    public function get($show_password, $by_email) {
        $this->query = $this->select_all . ' WHERE ';

        if ($by_email) {
            $this->query .= "Email = '" . $this->email . "'";
        } else {
            $this->query .= 'UserId = ' . $this->user_id;
        }

        $this->query .= $this->limit;

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();
        
        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        if ($this->user_id === null) {
            $this->user_id = $this->row_value('UserId');
        }

        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
        $this->email = $this->row_value('Email');
        $this->isAdmin = $this->row_value('IsAdmin');
        $this->isAdmin = $this->convert_to_boolean($this->isAdmin);
        $this->phone_num = $this->row_value('PhoneNumber');

        if ($show_password) {
            $this->password = $this->row_value('Password');
        }

    }

    public function update() {
        $this->clean_data();

        $this->stmt = $this->prepare_stmt("CALL updUser('{$this->user_first_name}', '{$this->user_last_name}', '{$this->email}','{$this->phone_num}', '{$this->user_id}');");

        return $this->stmt_executed();
    }

    public function passwords_confirm() {
        if ($this->password === $this->confirm_password) {
            return true;
        }

        return false;
    }

    public function password_meets_requirements() {
        if (preg_match('/^(?=.*[!@#$%^&*-])(?=.*\d)(?=.*[A-Z]).{8,20}$/', $this->password)) {
            return true;
        }

        return false;
    }

    public function login_to_correct_format($password = null) {
        $this->email = strval($this->email);
        
        if (is_null($password)) {
            $password = strval($password);
        } else {
            $this->password = strval($this->password);
        }
    }

    public function data_to_correct_format() {
        $this->login_to_correct_format();
        $this->user_first_name = strval($this->user_first_name);        
        $this->user_last_name = strval($this->user_last_name);
        $this->phone_num = strval($this->phone_num);
        $this->confirm_password = strval($this->confirm_password);
    }

    public function login_too_long() {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->format_status();
            $this->status .= 'Email address is not in the correct format';
        }

        if (strlen($this->email) > 255) {
            $this->format_status();
            $this->status .= 'Email address' . $this->too_long;
        }
    }

    public function data_too_long() {
        $this->login_too_long();

        if (strlen($this->user_first_name) > 255) {
            $this->status .= 'First name' . $this->too_long;
        }

        if (strlen($this->user_last_name) > 255) {
            $this->format_status();
            $this->status .= 'Last name' . $this->too_long;
        }
    }

    public function login_empty($password = null) {
        if ($this->email === '' || ($this->email === null)) {
            $this->format_status();
            $this->status .= 'Email address' . $this->cannot_empty;
        }

        if (($password === '' || $password === null) && ($this->password === '' || $this->password === null)) {
            $this->format_status();
            $this->status .= 'Password' . $this->cannot_empty;
        }
    }

    public function data_is_empty() {
        $this->login_empty();

        if (strlen($this->user_first_name) === 0 || ($this->user_first_name === null)) {
            $this->format_status();
            $this->status .= 'First name' . $this->cannot_empty;
        }

        if (strlen($this->user_last_name) === 0 || ($this->user_last_name === null)) {
            $this->format_status();
            $this->status .= 'Last name' . $this->cannot_empty;
        }

        if ((strlen($this->phone_num) !== 10) || ($this->phone_num === null)) {
            $this->format_status();
            $this->status .= 'Phone number must be exactly 10 characters';
        }
    }

    public function verify_password($password) {
        if (password_verify($password, $this->password)) {
            return true;
        } else {
            $this->format_status();
            $this->status .= 'Password is correct';
            
            return false;
        }
    }
    

    private function clean_data() {
        $this->user_first_name = htmlspecialchars(strip_tags($this->user_first_name));
        $this->user_last_name = htmlspecialchars(strip_tags($this->user_last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone_num = htmlentities(strip_tags($this->phone_num));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->isAdmin = htmlspecialchars(strip_tags($this->isAdmin));
    }

    private function encrypt_password() {
        return password_hash($this->password, PASSWORD_BCRYPT, $this->options);
    }
}