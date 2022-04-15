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

    public function get($show_password) {
        $this->query = $this->select_all . ' WHERE UserId = ' . $this->user_id . $this->limit;

        $this->stmt = $this->prepare_stmt($this->query);
        $this->execute();
        
        $this->row = $this->stmt->fetch(PDO::FETCH_ASSOC);

        $this->user_first_name = $this->row_value('FirstName');
        $this->user_last_name = $this->row_value('LastName');
        $this->email = $this->row_value('Email');
        $this->isAdmin = $this->row_value('IsAdmin');

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