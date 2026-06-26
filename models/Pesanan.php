<?php

require_once "config/Database.php";

class Pesanan extends Database
{
    public function generateKode()
    {
        return "ORD-" . date("YmdHis");
    }

    /*
    =====================================
    SIMPAN PESANAN BARU
    ==================================
    */

    public function create($data)
    {
        mysqli_report(
            MYSQLI_REPORT_ERROR |
            MYSQLI_REPORT_STRICT
        );

        $conn = $this->connect();

        $conn->begin_transaction();

        try {

            $kodePesanan = $this->generateKode();

            $userId = $data['user_id'];
            $idMenu = $data['id_menu'];

            $namaPenerima = $data['nama_pelanggan'];
            $jumlah = $data['jumlah'];

            $metodePembayaran =
            $data['metode_pembayaran'];
           
            /*
            ============================
            AMBIL DATA MENU
            ============================
            */

            $stmtMenu = $conn->prepare(
                "SELECT *
                 FROM menu
                 WHERE id=?"
            );

            $stmtMenu->bind_param(
                "i",
                $idMenu
            );

            $stmtMenu->execute();

            $menu =
            $stmtMenu
            ->get_result()
            ->fetch_assoc();

            if(!$menu)
            {
                throw new Exception(
                    "Menu tidak ditemukan"
                );
            }

            $harga =
            $menu['harga'];

            $subtotal =
            $harga * $jumlah;

            $totalHarga =
            $subtotal;

            /*
            ============================
            INSERT PESANAN
            ============================
            */

            $statusPesanan =
            "Menunggu Pembayaran";

            $createdAt =
            date('Y-m-d H:i:s');

            $stmtPesanan =
            $conn->prepare(
                "INSERT INTO pesanan
                (
                    kode_pesanan,
                    user_id,
                    nama_penerima,
                    total_harga,
                    status,
                    created_at
                )
                VALUES
                (?,?,?,?,?,?)"
            );

            $stmtPesanan->bind_param(
                "sisiss",
                $kodePesanan,
                $userId,
                $namaPenerima,
                $totalHarga,
                $statusPesanan,
                $createdAt
            );

            $stmtPesanan->execute();

            $pesananId =
            $conn->insert_id;

            /*
            ============================
            INSERT DETAIL PESANAN
            ============================
            */

            $stmtDetail =
            $conn->prepare(
                "INSERT INTO detail_pesanan
                (
                    pesanan_id,
                    menu_id,
                    harga,
                    jumlah,
                    subtotal
                )
                VALUES
                (?,?,?,?,?)"
            );

            $stmtDetail->bind_param(
                "iiiii",
                $pesananId,
                $idMenu,
                $harga,
                $jumlah,
                $subtotal
            );

            $stmtDetail->execute();

            /*
            ============================
            INSERT PEMBAYARAN
            ============================
            */

            $statusPembayaran =
            "Belum Bayar";

            $stmtPembayaran =
            $conn->prepare(
                "INSERT INTO pembayaran
                (
                    pesanan_id,
                    metode_pembayaran,
                    jumlah_bayar,
                    status
                )
                VALUES
                (?,?,?,?)"
            );

            $stmtPembayaran->bind_param(
                "isis",
                $pesananId,
                $metodePembayaran,
                $totalHarga,
                $statusPembayaran
            );

            $stmtPembayaran->execute();

            /*
            ============================
            COMMIT
            ============================
            */

            $conn->commit();

            return true;

        } catch (Exception $e) {
            var_dump($e->getMessage());
            die;

            $conn->rollback();

            return false;
        }
    }

    /*
    =====================================
    AMBIL SEMUA PESANAN
    =====================================
    */

    public function getAll()
    {
        $conn = $this->connect();

        $query = "

            SELECT

                p.id,

                p.kode_pesanan,

                p.nama_penerima,

                p.total_harga,

                p.status,

                p.created_at,

                u.nama,

                m.nama_menu,

                dp.harga,

                dp.jumlah,

                dp.subtotal,

                pem.metode_pembayaran,

                pem.status
                AS status_pembayaran

            FROM pesanan p

            LEFT JOIN users u
            ON p.user_id = u.id

            LEFT JOIN detail_pesanan dp
            ON p.id = dp.pesanan_id

            LEFT JOIN menu m
            ON dp.menu_id = m.id

            LEFT JOIN pembayaran pem
            ON p.id = pem.pesanan_id

            ORDER BY p.id DESC

        ";

        return $conn->query($query);
    }

    public function getRiwayatByUserId($userId)
    {
        $conn = $this->connect();

        $stmt = $conn->prepare(
            "SELECT

                p.id,

                p.kode_pesanan,

                p.nama_penerima,

                p.total_harga,

                p.status AS status_pesanan,

                p.created_at AS tanggal,

                u.nama,

                m.nama_menu,

                dp.harga,

                dp.jumlah,

                dp.subtotal,

                pem.metode_pembayaran,

                pem.status AS status_pembayaran

            FROM pesanan p

            LEFT JOIN users u
            ON p.user_id = u.id

            LEFT JOIN detail_pesanan dp
            ON p.id = dp.pesanan_id

            LEFT JOIN menu m
            ON dp.menu_id = m.id

            LEFT JOIN pembayaran pem
            ON p.id = pem.pesanan_id

            WHERE p.user_id = ?

            ORDER BY p.id DESC"
        );

        $stmt->bind_param('i', $userId);
        $stmt->execute();

        return $stmt->get_result();
    }

    /*
    =====================================
    UPDATE STATUS PESANAN
    =====================================
    */

    public function updateStatus(
        $idPesanan,
        $statusPesanan,
        $statusPembayaran
    )
    {
        $conn = $this->connect();

        $conn->begin_transaction();

        try {

            $stmt1 =
            $conn->prepare(
                "UPDATE pesanan
                 SET status=?
                 WHERE id=?"
            );

            $stmt1->bind_param(
                "si",
                $statusPesanan,
                $idPesanan
            );

            $stmt1->execute();

            $stmt2 =
            $conn->prepare(
                "UPDATE pembayaran
                 SET status=?
                 WHERE pesanan_id=?"
            );

            $stmt2->bind_param(
                "si",
                $statusPembayaran,
                $idPesanan
            );

            $stmt2->execute();

            $conn->commit();

            return true;

        } catch(Exception $e) {

            $conn->rollback();

            return false;
        }
    }

    public function cancel($idPesanan, $userId = null)
    {
        $conn = $this->connect();
        $conn->begin_transaction();

        try {
            if ($userId !== null) {
                $stmtCheck = $conn->prepare(
                    "SELECT status, user_id
                     FROM pesanan
                     WHERE id=?"
                );
                $stmtCheck->bind_param(
                    "i",
                    $idPesanan
                );
                $stmtCheck->execute();

                $order = $stmtCheck
                    ->get_result()
                    ->fetch_assoc();
             if (
                    !$order ||
                    $order['user_id'] != $userId ||
                    $order['status'] !== 'Menunggu Pembayaran'
                ) {
                  
                    $conn->rollback();
                    return false;
                }
            }
            

            $statusPesanan = 'Dibatalkan';
            $statusPembayaran = 'Ditolak';

            $stmtPesanan = $conn->prepare(
                "UPDATE pesanan
                 SET status=?
                 WHERE id=?"
            );
            $stmtPesanan->bind_param(
                "si",
                $statusPesanan,
                $idPesanan
            );
            $stmtPesanan->execute();

            $stmtPembayaran = $conn->prepare(
                "UPDATE pembayaran
                 SET status=?
                 WHERE pesanan_id=?"
            );
            $stmtPembayaran->bind_param(
                "si",
                $statusPembayaran,
                $idPesanan
            );
            $stmtPembayaran->execute();

            $conn->commit();
            return true;

        } catch (Exception $e) {
            var_dump($e->getMessage());
            die;
            $conn->rollback();
            return false;
        }
    }

    /*
    =====================================
    DETAIL PESANAN
    =====================================
    */

    public function find($id)
    {
        $conn = $this->connect();

        $stmt =
        $conn->prepare(
            "SELECT *
             FROM pesanan
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