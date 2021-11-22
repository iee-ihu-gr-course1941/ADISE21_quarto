<?php

class Database
{
    private $sock = '/home/student/it/2018/it185291/mysql/run/mysql.sock';
    private $db_name = 'quarto';
    private $username = 'quarto';
    private $password = '123123';
    private $conn;

    public function connect()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                'mysql:unix_socket=' . $this->sock.';'.
                'dbname=' . $this->db_name,
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }
        return $this->conn;
    }
}
