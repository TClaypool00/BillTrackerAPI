<?php
class User extends BaseClass {
    private $options = [
        'cost' => 11
    ];

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
        $this->user_firstName = htmlspecialchars(strip_tags($this->user_firstName));
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