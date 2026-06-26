<?php

$auth = new AuthController();

if (isset($_POST['logout'])) {
    if ($auth->logout()) {
        echo "
        <script>
        window.location.href = 'index.php';
        </script>
        ";
        exit;
    }
}

$pesan = new PesananController();
$message = null;

if (isset($_POST['update_status'])) {
    $idPesanan = intval($_POST['id_pesanan']);
    $statusPesanan = $_POST['status_pesanan'];
    $statusPembayaran = $_POST['status_pembayaran'];

    if ($pesan->updateStatusPesanan($idPesanan, $statusPesanan, $statusPembayaran)) {
        $message = "Status pesanan #$idPesanan berhasil diperbarui.";
    } else {
        $message = "Gagal memperbarui status pesanan #$idPesanan.";
    }
}

$pesananResult = $pesan->getAllPesanan();
$pesananData = [];
$stats = [
    'pending' => 0,
    'success' => 0,
    'failed' => 0
];

if ($pesananResult) {
    while ($row = $pesananResult->fetch_assoc()) {
        $pesananData[] = $row;

        if ($row['status'] === 'Selesai') {
            $stats['success']++;
        } elseif ($row['status'] === 'Dibatalkan') {
            $stats['failed']++;
        } else {
            $stats['pending']++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NikmatRasa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* --- VARIABEL WARNA & UTILITY DARI style.css --- */
        :root {
            --primary: #e11d48;         
            --primary-hover: #be123c;   
            --primary-light: #fff1f2;  
            --primary-border: #ffe4e6;  
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --amber-100: #fef3c7;
            --amber-500: #f59e0b;
            --amber-800: #92400e;
            --emerald-100: #d1fae5;
            --emerald-600: #059669;
            --transition: all 0.3s ease;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--slate-50);
            color: var(--slate-600);
            line-height: 1.5;
            padding-bottom: 60px;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* --- HEADER SIMULASI --- */
        .header {
            height: 70px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--slate-100);
            display: flex;
            align-items: center;
            box-shadow: var(--shadow-sm);
            margin-bottom: 40px;
        }

        .header .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            background-color: var(--primary);
            color: #ffffff;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.5px;
        }

        .logo-text span {
            color: var(--primary);
        }

        .btn-auth {
            padding: 8px 16px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--slate-900);
            background-color: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 8px;
            cursor: pointer;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-auth:hover {
            color: var(--primary);
            border-color: var(--primary-border);
        }

        /* --- ADMIN STYLES --- */
        .admin-title-section {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .admin-title-section h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--slate-900);
        }

        .admin-title-section p {
            font-size: 0.85rem;
            color: var(--slate-400);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #ffffff;
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--slate-100);
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-info h3 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--slate-900);
        }

        .stat-info p {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Badge status styling */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-pending {
            background-color: var(--amber-100);
            color: var(--amber-800);
        }

        .badge-success {
            background-color: var(--emerald-100);
            color: var(--emerald-600);
        }

        .badge-danger {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        /* Tabel data styling */
        .table-container {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid var(--slate-100);
            box-shadow: var(--shadow-md);
            overflow-x: auto;
            margin-bottom: 40px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.85rem;
        }

        .admin-table th {
            background-color: var(--slate-50);
            padding: 16px 20px;
            font-weight: 700;
            color: var(--slate-500);
            border-bottom: 1px solid var(--slate-100);
        }

        .admin-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--slate-50);
            color: var(--slate-700);
        }

        .admin-table tr:hover td {
            background-color: var(--slate-50);
        }

        /* Dropdown status update form */
        .status-select {
            padding: 6px 10px;
            border: 1px solid var(--slate-200);
            border-radius: 8px;
            font-size: 0.75rem;
            background-color: var(--slate-50);
            cursor: pointer;
            transition: var(--transition);
        }

        .status-select:focus {
            border-color: var(--primary);
            background-color: #ffffff;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background-color: var(--primary);
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 8px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
        }

        .notification-banner {
            margin-bottom: 24px;
            padding: 18px 22px;
            border-radius: 16px;
            border: 1px solid rgba(16, 185, 129, 0.25);
            background-color: #ecfdf5;
            color: #065f46;
            font-size: 0.95rem;
            box-shadow: var(--shadow-sm);
        }

        .notification-banner strong {
            display: block;
            font-weight: 800;
            margin-bottom: 6px;
            color: #047857;
        }

        /* --- FOOTER --- */
        .footer {
            background-color: var(--slate-900);
            color: var(--slate-400);
            padding: 30px 0;
            text-align: center;
            font-size: 0.75rem;
            border-top: 1px solid var(--slate-800);
            margin-top: 60px;
        }

        /* --- CUSTOM ALERT DIALOG --- */
        .alert-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.5);
            z-index: 3000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .alert-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        .alert-card {
            background-color: #ffffff;
            border-radius: 20px;
            padding: 24px;
            max-width: 360px;
            width: 100%;
            text-align: center;
            box-shadow: var(--shadow-xl);
            transform: scale(0.95);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .alert-overlay.open .alert-card {
            transform: scale(1);
        }

        .alert-icon-box {
            width: 56px;
            height: 56px;
            background-color: var(--emerald-100);
            color: var(--emerald-600);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            font-size: 1.5rem;
        }

        .alert-content h3 {
            font-size: 1rem;
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: 6px;
        }

        .alert-content p {
            font-size: 0.75rem;
            color: var(--slate-500);
            line-height: 1.5;
            text-align: center;
            background-color: var(--slate-50);
            padding: 10px;
            border-radius: 8px;
            border: 1px dashed var(--slate-200);
        }

        .btn-alert-close {
            padding: 10px;
            background-color: var(--primary);
            color: #ffffff;
            font-weight: 700;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.75rem;
            transition: var(--transition);
        }

        .btn-alert-close:hover {
            background-color: var(--primary-hover);
        }
    </style>
</head>
<body>

    <!-- Header Simulasi -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <span class="logo-icon">
                    <i class="fa-solid fa-utensils"></i>
                </span>
                <span class="logo-text">Nikmat<span>Rasa</span></span>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 0.85rem; font-weight: 600; color: var(--slate-600);">Administrator: <strong>Elisabeth</strong></span>
                <form  method="post">
                    <button name="logout" type="submit"  class="btn-auth" style="color: var(--primary); border-color: var(--primary-border); background-color: var(--primary-light);">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
                
            </div>
        </div>
    </header>

    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="notification-banner">
                <strong>Notifikasi</strong>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Title Section -->
        <div class="admin-title-section">
            <div>
                <h1>Dashboard Admin</h1>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <a href="index.php?page=admin_menu" class="btn-primary"><i class="fa-solid fa-utensils"></i> Kelola Menu</a>
            </div>
        </div>

        <!-- Ringkasan Notifikasi / Status Panel -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background-color: var(--amber-100); color: var(--amber-500);">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div class="stat-info">
                    <h3 id="count-pending"><?= $stats['pending'] ?></h3>
                    <p>Menunggu Pembayaran</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background-color: var(--emerald-100); color: var(--emerald-600);">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="stat-info">
                    <h3 id="count-success"><?= $stats['success'] ?></h3>
                    <p>Pesanan Selesai</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background-color: var(--primary-light); color: var(--primary);">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div class="stat-info">
                    <h3 id="count-failed"><?= $stats['failed'] ?></h3>
                    <p>Dibatalkan</p>
                </div>
            </div>
        </div>

        <!-- Tabel Monitoring Pesanan -->
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Tanggal</th>
                        <th>Nama Pelanggan</th>
                        <th>Detail Menu</th>
                        <th>Metode Bayar</th>
                        <th>Total Pembayaran</th>
                        <th>Status Pesanan</th>
                        <th>Status Pembayaran</th>
                        <th style="text-align: center;">Tindakan / Validasi</th>
                    </tr>
                </thead>
                <tbody id="pesanan-tbody">
                    <?php if (empty($pesananData)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: var(--slate-400);">
                                Tidak ada pesanan ditemukan di database.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1 ?>
                        <?php foreach ($pesananData as $row): ?>
                            <tr id="row-<?= htmlspecialchars($row['id']) ?>">
                                <td style="font-weight: 700; color: var(--slate-900);">#<?= $no++ ?></td>
                                <td style="font-size: 0.75rem;"><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_menu']) ?></strong>
                                    <span style="font-size: 0.75rem; color: var(--slate-400); display: block;">Jumlah: <?= htmlspecialchars($row['jumlah']) ?> Porsi</span>
                                </td>
                                <td><span style="font-size: 0.75rem; font-weight: 600;"><?= htmlspecialchars($row['metode_pembayaran']) ?></span></td>
                                <td style="font-weight: 800; color: var(--slate-900);">Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                <td class="cell-status-pesanan">
                                    <?php if ($row['status'] === 'Selesai'): ?>
                                        <span class="badge badge-success"><i class="fa-solid fa-check"></i> Selesai</span>
                                    <?php elseif ($row['status'] === 'Batal'): ?>
                                        <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> Batal</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending"><i class="fa-solid fa-spinner fa-spin"></i> <?= htmlspecialchars($row['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="cell-status-pembayaran">
                                    <?php if ($row['status_pembayaran'] === 'Lunas'): ?>
                                        <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Lunas</span>
                                    <?php elseif ($row['status_pembayaran'] === 'Batal'): ?>
                                        <span class="badge badge-danger"><i class="fa-solid fa-circle-xmark"></i> Batal</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($row['status_pembayaran']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <form method="post" style="display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; align-items: center;">
                                        <input type="hidden" name="id_pesanan" value="<?= htmlspecialchars($row['id']) ?>">
                                        <select class="status-select" name="status_pesanan">
                                            <option value="Menunggu Pembayaran" <?= $row['status'] === 'Menunggu Pembayaran' ? 'selected' : '' ?>>Menunggu Pembayaran</option>
                                            <option value="Selesai" <?= $row['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                            <option value="Dibatalkan" <?= $row['status'] === 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                        </select>
                                        <select class="status-select" name="status_pembayaran">
                                            <option value="Belum Bayar" <?= $row['status_pembayaran'] === 'Belum Bayar' ? 'selected' : '' ?>>Belum Bayar</option>
                                            <option value="Lunas" <?= $row['status_pembayaran'] === 'Lunas' ? 'selected' : '' ?>>Lunas</option>
                                            <option value="Ditolak" <?= $row['status_pembayaran'] === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn-primary">Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>NikmatRasa</p>
            <p>&copy; 2026 NikmatRasa</p>
        </div>
    </footer>

</body>
</html>

