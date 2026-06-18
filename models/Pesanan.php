<?php

require_once "../config/Database.php";

class Pesanan extends Database
{
    public function generateKode()
    {
        return
        "ORD-" .
        date("YmdHis");
    }

    public function create(
        $data
    )
    {
        $conn = $this->connect();

        $kode =
        $this->generateKode();

        $stmt = $conn->prepare(
            "INSERT INTO pesanan
            (
                kode_pesanan,
                user_id,
                metode_pengiriman,
                alamat_pengiriman,
                total_harga
            )
            VALUES
            (?,?,?,?,?)"
        );

        $stmt->bind_param(
            "sissi",
            $kode,
            $data['user_id'],
            $data['metode'],
            $data['alamat'],
            $data['total']
        );

        return $stmt->execute();
    }
}

