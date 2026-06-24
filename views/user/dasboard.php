<?php
// Proteksi halaman agar hanya user pembeli yang sudah login bisa masuk


$pesanController = new PesananController();
$userId = $_SESSION['user_id']; // Mengambil ID user dari sesi login aktif

//Mengambil riwayat transaksi belanja user tersebut
$riwayatPesanan = $pesanController->getRiwayatBelanjaUser($userId);

$pesananData = [];
$totalBelanja = 0;
$jumlahPesananSelesai = 0;

if ($riwayatPesanan) {
    while ($row = $riwayatPesanan->fetch_assoc()) {
        $pesananData[] = $row;
        if ($row['status_pesanan'] == 'Selesai' && $row['status_pembayaran'] == 'Lunas') {
            $totalBelanja += $row['total_harga'];
            $jumlahPesananSelesai++;
        }
    }
}

include 'views/layouts/header.php';
?>

<style>
    .user-hero {
        padding: 120px 0 60px;
        background: radial-gradient(circle at 90% 10%, var(--primary-light) 0%, rgba(255,255,255,0) 40%),
                    radial-gradient(circle at 10% 90%, var(--amber-100) 0%, rgba(255,255,255,0) 30%);
    }

    .hero-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 40px;
        align-items: center;
    }

    @media (min-width: 768px) {
        .hero-grid {
            grid-template-columns: 1.2fr 0.8fr;
        }
    }

    .hero-content h1 {
        font-size: 2.5rem;
        font-weight: 900;
        color: var(--slate-900);
        line-height: 1.1;
        margin-bottom: 18px;
    }

    .hero-desc {
        max-width: 640px;
        color: var(--slate-500);
        font-size: 1rem;
        line-height: 1.8;
    }

    .hero-graphic {
        display: flex;
        justify-content: center;
    }

    .graphic-card {
        background-color: #ffffff;
        padding: 28px;
        border-radius: 24px;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--slate-100);
        width: 100%;
        max-width: 420px;
    }

    .graphic-info h4 {
        font-size: 1rem;
        font-weight: 800;
        color: var(--slate-900);
        margin-bottom: 6px;
    }

    .graphic-info p {
        font-size: 0.9rem;
        color: var(--slate-500);
        line-height: 1.6;
    }

    .graphic-rating {
        font-size: 0.75rem;
        font-weight: 700;
        background-color: var(--amber-100);
        color: var(--amber-800);
        padding: 6px 12px;
        border-radius: 999px;
    }

    .order-summary {
        display: flex;
        gap: 16px;
        margin-top: 26px;
        flex-wrap: wrap;
    }

    .summary-card {
        background-color: #ffffff;
        border: 1px solid var(--slate-100);
        padding: 20px 22px;
        border-radius: 20px;
        min-width: 150px;
        box-shadow: var(--shadow-sm);
        flex: 1;
    }

    .summary-card h3 {
        font-size: 1.75rem;
        font-weight: 900;
        color: var(--slate-900);
        margin-bottom: 6px;
    }

    .summary-card p {
        font-size: 0.8rem;
        color: var(--slate-500);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 700;
    }

    .order-section {
        padding: 80px 0;
        background-color: #ffffff;
    }

    .order-header {
        margin-bottom: 28px;
    }

    .order-header h2 {
        font-size: 1.9rem;
        font-weight: 900;
        color: var(--slate-900);
        margin-bottom: 12px;
    }

    .order-header p {
        color: var(--slate-500);
        font-size: 0.95rem;
        line-height: 1.7;
        max-width: 700px;
    }

    .order-history-grid {
        display: grid;
        gap: 24px;
    }

    @media (min-width: 768px) {
        .order-history-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .order-card {
        background-color: var(--slate-50);
        border-radius: 24px;
        padding: 24px;
        border: 1px solid var(--slate-100);
        box-shadow: var(--shadow-sm);
    }

    .order-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .order-meta span {
        display: inline-flex;
        gap: 8px;
        align-items: center;
        padding: 8px 12px;
        background-color: #ffffff;
        border-radius: 14px;
        border: 1px solid var(--slate-100);
        color: var(--slate-500);
        font-size: 0.85rem;
    }

    .order-card h3 {
        margin-bottom: 18px;
        font-size: 1rem;
        color: var(--slate-800);
    }

    .order-details {
        display: grid;
        gap: 12px;
    }

    .order-detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--slate-600);
        font-size: 0.95rem;
    }

    .order-detail-item strong {
        color: var(--slate-900);
    }

    .order-empty {
        text-align: center;
        padding: 70px 24px;
        border: 1px dashed var(--slate-200);
        border-radius: 24px;
        color: var(--slate-400);
        background-color: #f8fafc;
    }

    .order-empty i {
        font-size: 3rem;
        margin-bottom: 16px;
        color: var(--primary);
    }
</style>

<main>
    <section class="hero user-hero">
        <div class="container">
            <div class="hero-grid">
                <div>
                    <span class="hero-badge">Dashboard Pelanggan</span>
                    <h1 class="hero-title">Halo, <?= htmlspecialchars($_SESSION['nama']) ?>!</h1>
                    <p class="hero-desc">Sesuai dengan gaya landing page, halaman ini menampilkan status pesanan Anda dengan tampilan yang bersih, modern, dan mudah dibaca.</p>
                    <div style="margin-top: 30px; display: flex; flex-wrap: wrap; gap: 14px;">
                        <a href="index.php" class="btn-primary">
                            Pesan Menu Baru <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="hero-graphic">
                    <div class="graphic-card">
                        <div class="graphic-info">
                            <div>
                                <h4>Ringkasan Pesanan</h4>
                                <p>Jumlah pesanan selesai dan total belanja Anda ditampilkan secara langsung di sini.</p>
                            </div>
                            <span class="graphic-rating">Akun User</span>
                        </div>
                        <div class="order-summary">
                            <div class="summary-card">
                                <h3><?= $jumlahPesananSelesai ?></h3>
                                <p>Pesanan Selesai</p>
                            </div>
                            <div class="summary-card">
                                <h3>Rp <?= number_format($totalBelanja, 0, ',', '.') ?></h3>
                                <p>Total Belanja</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="order-section">
        <div class="container">
            <div class="order-header">
                <h2>Status Pesanan Anda</h2>
                <p>Semua riwayat pesanan Anda ditampilkan dengan gaya yang konsisten dan selaras dengan landing page.</p>
            </div>

            <?php if (empty($pesananData)): ?>
                <div class="order-empty">
                    <i class="fa-solid fa-receipt"></i>
                    <p>Anda belum pernah memesan makanan. Yuk pilih menu nusantara favoritmu sekarang!</p>
                </div>
            <?php else: ?>
                <div class="order-history-grid">
                    <?php foreach ($pesananData as $row): ?>
                        <article class="order-card">
                            <div class="order-meta">
                                <span><i class="fa-solid fa-hashtag"></i> <?= htmlspecialchars($row['kode_pesanan']) ?></span>
                                <span><i class="fa-solid fa-calendar-days"></i> <?= date('d M Y, H:i', strtotime($row['tanggal'])) ?></span>
                                <span><i class="fa-solid fa-bowl-food"></i> <?= htmlspecialchars($row['nama_menu']) ?></span>
                            </div>
                            <h3>Detail Pesanan</h3>
                            <div class="order-details">
                                <div class="order-detail-item">
                                    <span>Jumlah</span>
                                    <strong><?= $row['jumlah'] ?> Porsi</strong>
                                </div>
                                <div class="order-detail-item">
                                    <span>Metode Pembayaran</span>
                                    <strong><?= htmlspecialchars($row['metode_pembayaran']) ?></strong>
                                </div>
                                <div class="order-detail-item">
                                    <span>Total Bayar</span>
                                    <strong>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                            <div class="order-meta" style="margin-top: 20px;">
                                <span class="badge <?= $row['status_pesanan'] == 'Selesai' ? 'badge-success' : ($row['status_pesanan'] == 'Proses' ? 'badge-pending' : 'badge-danger') ?>">
                                    <i class="fa-solid <?= $row['status_pesanan'] == 'Selesai' ? 'fa-check' : ($row['status_pesanan'] == 'Proses' ? 'fa-spinner fa-spin' : 'fa-xmark') ?>"></i>
                                    <?= $row['status_pesanan'] == 'Selesai' ? 'Selesai' : ($row['status_pesanan'] == 'Proses' ? 'Sedang Diproses' : 'Batal') ?>
                                </span>
                                <span class="badge <?= $row['status_pembayaran'] == 'Lunas' ? 'badge-success' : ($row['status_pembayaran'] == 'Belum Lunas' ? 'badge-pending' : 'badge-danger') ?>">
                                    <i class="fa-solid <?= $row['status_pembayaran'] == 'Lunas' ? 'fa-circle-check' : ($row['status_pembayaran'] == 'Belum Lunas' ? 'fa-circle-exclamation' : 'fa-circle-xmark') ?>"></i>
                                    <?= $row['status_pembayaran'] == 'Lunas' ? 'Lunas' : ($row['status_pembayaran'] == 'Belum Lunas' ? 'Belum Lunas' : 'Batal / Hangus') ?>
                                </span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'views/layouts/footer.php'; ?>