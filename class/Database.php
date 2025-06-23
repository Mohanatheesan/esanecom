<?php
require_once "../class/config.php";

class Database
{
    private $host = HOST;
    private $user = USER;
    private $pass = PASS;
    private $db = DB;

    public $con;

    public function __construct()
    {
        $this->getConnection();
    }

    public function getConnection()
    {
        $this->con = new mysqli($this->host, $this->user, $this->pass, $this->db);
        if ($this->con->connect_error) {
            die("Database connection failed: " . $this->con->connect_error);
        }
    }

    public function close()
    {
        $this->con->close();
    }
}
?>