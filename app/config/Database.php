<?php

include_once '../../config/Config.php';

class Database
{
    private $conn;

    public function connect()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                'mysql:unix_socket=' . Config::$sock .';dbname=' . Config::$db_name,
                Config::$username,
                Config::$password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }
        return $this->conn;
    }
}
