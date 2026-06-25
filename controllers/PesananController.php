<?php

require_once "models/Pesanan.php";

class PesananController
{
    private $pesanan;

    public function __construct()
    {
        // Instansiasi model Pesanan sesuai arsitektur OOP MVC Anda
        $this->pesanan = new Pesanan();
    }

    /**
     * Menangani pembuatan pesanan langsung dari form pelanggan (landing.php)
     */
    public function pesan()
    {
        if (isset($_POST['pesan'])) {
            /*
            ========================
            Ambil Data Form $_POST
            ========================
            */
            $idMenu = $_POST['id_menu'];
            $jumlah = $_POST['jumlah'];
            $namaPelanggan = $_POST['nama_pelanggan'];
            $metodePembayaran = $_POST['metode_pembayaran'];
            
            // Ambil total_harga dari input hidden form, atau hitung secara manual sebagai cadangan
            $totalHarga = isset($_POST['total_harga']) ? $_POST['total_harga'] : (25000 * $jumlah);

            /*
            ========================
            User Login Session Check
            ========================
            */
            $userId = $_SESSION['user_id'] ?? NULL;

            /*
            ========================
            Penyusunan Data Pesanan
            ========================
            */
            $data = [
                'user_id' => $userId,
                'id_menu' => $idMenu,
                'jumlah' => $jumlah,
                'nama_pelanggan' => $namaPelanggan,
                'metode_pembayaran' => $metodePembayaran,
                'total_harga' => $totalHarga,
                'tanggal_pesanan' => date('Y-m-d H:i:s')
            ];

          
            // Kirim data ke Model Pesanan untuk memproses transaksi database (multi-table insert)
            $simpan = $this->pesanan->create($data);
         

            if ($simpan) {
                echo "
                <script>
                    alert('Pesanan berhasil dibuat! Silahkan menunggu.');
                    window.location.href = 'index.php';
                </script>
                ";
                exit;
            } else {
                echo "
                <script>
                    alert('Pesanan gagal dibuat. Silakan coba lagi.');
                </script>
                ";
            }
        }
    }

    /**
     * Memanggil seluruh data pesanan untuk dashboard admin
     */
    public function getAllPesanan()
    {
        // Mendelegasikan pencarian data ke Model Pesanan
        return $this->pesanan->getAll();
    }

    /**
     * Mengambil riwayat pesanan untuk user yang sedang login
     */
    public function getRiwayatBelanjaUser($userId)
    {
        return $this->pesanan->getRiwayatByUserId($userId);
    }

    /**
     * Melakukan validasi & pembaruan status pesanan serta pembayaran (Aksi Admin)
     */
    public function updateStatusPesanan($id_pesanan, $status_pesanan, $status_pembayaran)
    {
        // Mendelegasikan pembaruan status ke Model Pesanan
        return $this->pesanan->updateStatus($id_pesanan, $status_pesanan, $status_pembayaran);
    }

    public function cancelPesanan($id_pesanan, $userId)
    {
        return $this->pesanan->cancel($id_pesanan, $userId);
    }
}
?>

