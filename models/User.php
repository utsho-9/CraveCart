<?php
require_once 'Database.php';

class User {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getUserByEmail($email) {
        $safe_email = mysqli_real_escape_string($this->conn, $email);

        $sql = "SELECT * FROM users WHERE email = '$safe_email' LIMIT 1";
        $result = mysqli_query($this->conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
        return false;
    }
}
?>