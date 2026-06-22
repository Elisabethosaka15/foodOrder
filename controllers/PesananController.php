<?php

require_once "models/Pesanan.php";

class PesananController
{
    private $pesanan;

    public function __construct()
    {
        $this->pesanan = new Pesanan();
    }

    public function pesan()
    {
        if(isset($_POST['pesan']))
        {
            /*
            ========================
            Ambil Data Form
            ========================
            */

            $jumlah = $_POST['jumlah'];
            $idMenu = $_POST['id_menu'];
            $namaPelanggan = $_POST['nama_pelanggan'];
            $metodePembayaran = $_POST['metode_pembayaran'];

            /*
            ========================
            Sementara
            ========================
            */

            $hargaMenu = 25000;

            $totalHarga =
            $hargaMenu * $jumlah;

            /*
            ========================
            User Login
            ========================
            */

            $userId =
            $_SESSION['user_id'] ?? 1;

            /*
            ========================
            Data Pesanan
            ========================
            */

            $data = [

                'user_id' => $userId,

                'metode' => 'Ambil Sendiri',

                'total' => $totalHarga

            ];

            $simpan =
            $this->pesanan
                 ->create($data);

            if($simpan)
            {
                echo "
                <script>
                    alert('Pesanan berhasil dibuat');
                    window.location='riwayat.php';
                </script>
                ";
            }
            else
            {
                echo "
                <script>
                    alert('Pesanan gagal dibuat');
                </script>
                ";
            }
        }
    }
}