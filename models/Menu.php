<?php

require_once "config/Database.php";

class Menu extends Database
{
    public function getAll()
    {
        $conn = $this->connect();

        $sql =
            "SELECT * FROM menu ORDER BY id DESC";

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

    public function create($data)
    {
        $conn = $this->connect();

        $stmt = $conn->prepare(
            "INSERT INTO menu
             (nama_menu, deskripsi, kategori, harga, stok, foto)
             VALUES
             (?,?,?,?,?,?)"
        );

        $stmt->bind_param(
            "sssdis",
            $data['nama_menu'],
            $data['deskripsi'],
            $data['kategori'],
            $data['harga'],
            $data['stok'],
            $data['foto']
        );

        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $conn = $this->connect();

        $stmt = $conn->prepare(
            "UPDATE menu
             SET nama_menu = ?,
                 deskripsi = ?,
                 kategori = ?,
                 harga = ?,
                 stok = ?,
                 foto = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "sssdisi",
            $data['nama_menu'],
            $data['deskripsi'],
            $data['kategori'],
            $data['harga'],
            $data['stok'],
            $data['foto'],
            $id
        );

        return $stmt->execute();
    }

    public function delete($id)
    {
        $conn = $this->connect();

        $stmt = $conn->prepare(
            "DELETE FROM menu
             WHERE id = ?"
        );

        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
}

