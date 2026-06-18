<?php

class Database
{
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db   = "foodorder";

    protected $conn;

    public function connect()
    {
        if (!$this->conn)
        {
            $this->conn = new mysqli(
                $this->host,
                $this->user,
                $this->pass,
                $this->db
            );

            if ($this->conn->connect_error)
            {
                die(
                    "Koneksi gagal : "
                    . $this->conn->connect_error
                );
            }
        }

        return $this->conn;
    }
}

