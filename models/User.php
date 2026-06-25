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

        $role = 'user';

        $stmt = $conn->prepare(
            "INSERT INTO users
            (
                nama,
                username,
                email,
                password,
                role
            )
            VALUES
            (?,?,?,?,?)"
        );

        $stmt->bind_param(
            "sssss",
            $nama,
            $username,
            $email,
            $hash,
            $role
        );

        return $stmt->execute();
    }

    public function getByEmail(
        $email
    )
    {
        $conn = $this->connect();

        $stmt = $conn->prepare(
            "SELECT *
             FROM users
             WHERE email=?"
        );

        $stmt->bind_param(
            "s",
            $email
        );

        $stmt->execute();

        return $stmt
                ->get_result()
                ->fetch_assoc();
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

