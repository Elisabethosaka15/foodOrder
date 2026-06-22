<?php

require_once "config/Database.php";

class Menu extends Database
{
    public function getAll()
    {
        $conn = $this->connect();

        $sql =
        "SELECT * FROM menu";

        return $conn->query($sql);
    }

    public function find($id)
    {
        $conn = $this->connect();

        $stmt = $conn->prepare(
            "SELECT * FROM menu
             WHERE id=?"
        );

        $stmt->bind_param(
            "i",
            $id
        );

        $stmt->execute();

        return $stmt
                ->get_result()
                ->fetch_assoc();
    }
}

