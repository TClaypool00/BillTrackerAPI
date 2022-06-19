# Bill Tracker API

## Prerequisites
### Download XAMPP
1. If you do not have installed already, please install XAMPP. You can do so by going to by click this link https://www.apachefriends.org/
2. Follow the onscreen directions.
3. Once it is installed, run the XAMPP appliaction. (It is recommend that you are an adminstrator)

### Creating the Bill Tracker database
1. Click the "Start" button beside "Appache" and "MySQL"
2. Click the "Admin" button beside "MySQL", it will take you to phpMyAdmin website.
3. Click the SQL tab at the top.
4. Copy the contents from the "billtracker.sql" file and paste it into the whitespace in the phpMyAdmin

### Additional files
1. You will to create "config" folder. (case sensitive) It needs to be in the root directory
2. Inside the config folder. Create a file called "Database.php" and "Secret.php" (both case sensitive)
#### Database.php
```php
<?php
class Database {
    private $host = 'your host';
    private $db_name = 'billtracker';
    private $username = 'your username';
    private $password = 'your password';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Connection error: ' . $e->getMessage();
        }

        return $this->conn;
    }
}
```

#### Secret.php
```php
<?php
class Secret {
    public static $key = 'Your secret key';
    public static $alg = 'HS256';

    public $token;

    private $issuer;
    private $audience = 'your audience';
    private $issued_at;
    private $not_before_claim;
    private $expire_claim;
    
    public function __construct($id, $is_Admin) {
        $this->issuer = 'your issuer';
        $this->issued_at = time();
        $this->not_before_claim = $this->issued_at + 10;
        $this->expire_claim = $this->issued_at + ((60 * 60) * 2); // You can set the time to however long you want

        $this->token = array(
            'iss' => $this->issuer,
            'iat' => $this->issued_at,
            'nbf' => $this->not_before_claim,
            'exp' => $this->expire_claim,
            'aud' => $this->audience,
            'userId' => $id,
            'isAdmin' => $is_Admin
        );
    }
}

```