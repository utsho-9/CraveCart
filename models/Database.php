<?php

class Database {
    private $host = "127.0.0.1";
    private $db_name = "cravecart_db";
    private $username = "root";
    private $password = ""; 
    public $conn;

    public function getConnection() {
        $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->db_name);

        if (!$this->conn) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        return $this->conn;
    }
}
?>