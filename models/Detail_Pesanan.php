<?php

require_once "config/Database.php";

$db = new Database();
$conn = $db->connect();

$id = $_GET['id'] ?? 0;

/*
=====================
DATA PESANAN
=====================
*/
$stmt = $conn->prepare("
    SELECT *
    FROM pesanan
    WHERE id=?
");

$stmt->bind_param("i", $id);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();

if (!$pesanan) {
    echo "Pesanan tidak ditemukan";
    exit;
}

/*
=====================
DETAIL PESANAN
=====================
*/
$stmtDetail = $conn->prepare("
    SELECT dp.*, m.nama_menu
    FROM detail_pesanan dp
    LEFT JOIN menu m ON dp.menu_id = m.id
    WHERE dp.pesanan_id=?
");

$stmtDetail->bind_param("i", $id);
$stmtDetail->execute();
$detail = $stmtDetail->get_result();

/*
=====================
PEMBAYARAN
=====================
*/
$stmtBayar = $conn->prepare("
    SELECT *
    FROM pembayaran
    WHERE pesanan_id=?
");

$stmtBayar->bind_param("i", $id);
$stmtBayar->execute();
$pembayaran = $stmtBayar->get_result()->fetch_assoc();
?>

