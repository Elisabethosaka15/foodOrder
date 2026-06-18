<?php

require_once "config/Database.php";

class User extends Database
{
    private $table = "users";

    public function create(
        $nama,
        $username,
        $email,
        $password
    )
    {
        $conn = $this->connect();

        $hash =
        password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $conn->prepare(
            "INSERT INTO users
            (
                nama,
                username,
                email,
                password
            )
            VALUES
            (?,?,?,?)"
        );

        $stmt->bind_param(
            "ssss",
            $nama,
            $username,
            $email,
            $hash
        );

        return $stmt->execute();
    }

    public function getByUsername(
        $username
    )
    {
        $conn = $this->connect();

        $stmt = $conn->prepare(
            "SELECT *
             FROM users
             WHERE username=?"
        );

        $stmt->bind_param(
            "s",
            $username
        );

        $stmt->execute();

        return $stmt
                ->get_result()
                ->fetch_assoc();
    }
}

