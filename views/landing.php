<?php include './views/layouts/header.php';

$auth = new AuthController();
$pesan = new PesananController();
$menu = new MenuController();

// MEMPERBAIKI LOGIKA LOGOUT: Hanya berjalan jika tombol logout ditekan
if (isset($_POST['logout'])) {
    if ($auth->logout()) {
        echo "
        <script>
        alert('Logout Berhasil');
        window.location.href = 'index.php';
        </script>
        ";
        exit;
    }
}
?>

<header class="header" id="main-header">
    <div class="container">
        <div class="logo">
            <span class="logo-icon">
                <i class="fa-solid fa-utensils"></i>
            </span>
            <span class="logo-text">Rasa<span>Nusantara</span></span>
        </div>

        <nav class="nav">
            <a href="#beranda" class="nav-link active" id="nav-beranda">Beranda</a>
            <a href="#menu" class="nav-link" id="nav-menu">Daftar Menu</a>
        </nav>

        <div class="user-action">
            <?php if (isset($_SESSION['nama'])) : ?>
                <!-- Mengatur wrapper agar elemen teks nama dan tombol sejajar secara vertikal -->
                <div class="user-wrap" style="display: flex; align-items: center; gap: 12px;">
                    <span class="user-label" style="display: inline; font-size: 0.85rem; font-weight: 600; color: var(--slate-700);">
                        Halo, <?= $_SESSION['nama'] ?>
                    </span>

                    <!-- Menggunakan form dengan margin reset agar tidak mengacaukan tinggi flexbox header -->
                    <form method="post" action="" style="margin: 0; display: inline-block;">
                        <!-- Menggunakan class btn-auth dengan sedikit custom inline style berwarna merah agar kontras dan elegan -->
                        <button name="logout" type="submit" class="btn-auth" style="color: var(--primary); border-color: var(--primary-border); background-color: var(--primary-light); display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <a href="index.php?page=login" class="btn-auth">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk / Daftar
                </a>
            <?php endif ?>
        </div>
    </div>
</header>

<main>

    <section id="beranda" class="hero">
        <div class="container">
            <div class="hero-grid">

                <div class="hero-content">
                    <h1 class="hero-title">
                        Pesan Makanan <br>Favoritmu <span>Secara Instan</span>
                    </h1>
                    <p class="hero-desc">
                        Pilih hidangan nusantara favorit Anda di bawah, isi detail pesanan, pilih metode pembayaran, dan pesanan Anda langsung diproses ke dapur kami.
                    </p>
                    <a href="#menu" class="btn-primary">
                        Pilih Menu Hidangan <i class="fa-solid fa-arrow-down"></i>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <section id="menu" class="menu-section">
        <div class="container">

            <div class="menu-header">
                <div class="menu-title-block">
                    <h2>Daftar Menu Hidangan</h2>
                    <p>Sistem Pemesanan Langsung terintegrasi dengan tabel database</p>
                </div>

                <div class="filter-container">
                    <button onclick="filterMenu('semua')" class="menu-filter-btn active" data-category="semua">Semua</button>
                    <button onclick="filterMenu('Makanan')" class="menu-filter-btn" data-category="Makanan">Makanan</button>
                    <button onclick="filterMenu('Minuman')" class="menu-filter-btn" data-category="Minuman">Minuman</button>
                </div>
            </div>

            <div class="menu-grid" id="menu-container">
                <?php $result = $menu->getMenu()?>

                <?php while ($row = $result->fetch_assoc()) { ?>
                    <div class="menu-item" data-category="<?= $row['kategori']; ?>" data-id="<?= $row['id']; ?>">
                        <div class="item-media">
                            <i class="fa-solid fa-bowl-rice"></i>
                            <span class="item-db-id">ID Menu: <?= $row['id']; ?></span>
                        </div>

                        <div class="item-content">
                            <h3 class="item-title"><?= $row['nama_menu']; ?></h3>

                            <p class="item-desc">
                                <?= $row['deskripsi']; ?>
                            </p>

                            <div class="item-action-area">
                                <span class="item-price">
                                    Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                </span>

                                <button
                                    onclick="openOrderModal(
                                        <?= $row['id']; ?>,
                                        '<?= addslashes($row['nama_menu']); ?>',
                                        <?= $row['harga']; ?>
                                    )"
                                    class="btn-order">
                                    Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    </section>

</main>

<div id="order-modal" class="modal-overlay">
    <div class="modal-card" id="order-card">

        <div class="modal-header">
            <div class="modal-header-text">
                <h3><i class="fa-solid fa-file-invoice" style="color: var(--primary);"></i> Form Pemesanan</h3>
                <p>Memasukkan data ke tabel: <span>pesanan, detail_pesanan, pembayaran</span></p>
            </div>
            <button onclick="closeOrderModal()" class="btn-close-modal">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="selected-item-box">
            <span>Menu Pilihan Anda</span>
            <div class="selected-item-detail">
                <h4 id="selected-menu-name" class="selected-item-name">Nama Menu</h4>
                <span id="selected-menu-price-text" class="selected-item-price">Rp 0</span>
            </div>
        </div>

        <form id="direct-order-form" method="post" action="">
            <input type="hidden" id="input-menu-id" name="id_menu">
            <input type="hidden" id="input-menu-harga">

            <div class="form-group">
                <label class="form-label">Jumlah Porsi (Tabel: detail_pesanan)</label>
                <div class="qty-control">
                    <button type="button" onclick="adjustQty(-1)" class="btn-qty">-</button>
                    <input type="number" id="input-qty" name="jumlah" value="1" min="1" class="input-qty-number" readonly>
                    <button type="button" onclick="adjustQty(1)" class="btn-qty">+</button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Lengkap Pelanggan (Tabel: users)</label>
                <input type="text" id="customer-name" name="nama_pelanggan" placeholder="Masukkan nama lengkap Anda" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Metode Pembayaran (Tabel: pembayaran)</label>
                <select id="payment-method" name="metode_pembayaran" class="form-input" style="cursor: pointer;">
                    <option value="Transfer Bank">Transfer Bank / Virtual Account</option>
                    <option value="E-Wallet">E-Wallet (OVO / GoPay / Dana)</option>
                    <option value="Tunai">Bayar Tunai di Kasir (COD)</option>
                </select>
            </div>

            <div class="total-payment-row">
                <span>Total Pembayaran:</span>
                <span id="order-total-price">Rp 0</span>
            </div>

            <button type="submit" class="btn-submit-order" name="pesan">
                <i class="fa-solid fa-circle-check"></i> Kirim & Konfirmasi Pesanan
            </button>
        </form>

    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="footer-main-text">RasaNusantara - Landing Page PHP Native MVC Framework</p>
        <p>&copy; 2026 RasaNusantara. Integrasi tabel database: <code>menu, users, pesanan, detail_pesanan, pembayaran</code></p>
    </div>
</footer>

<div id="custom-alert" class="alert-overlay">
    <div class="alert-card">
        <div class="alert-icon-box">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="alert-content">
            <h3 id="alert-title">Pesanan Berhasil</h3>
            <p id="alert-message">Detail pesan sukses simulasi database.</p>
        </div>
        <button onclick="closeAlert()" class="btn-alert-close">
            Tutup Jendela
        </button>
    </div>
</div>

<script>
    // DOM Elements
    const mainHeader = document.getElementById('main-header');
    const orderModal = document.getElementById('order-modal');
    const selectedMenuName = document.getElementById('selected-menu-name');
    const selectedMenuPriceText = document.getElementById('selected-menu-price-text');
    const inputMenuId = document.getElementById('input-menu-id');
    const inputMenuHarga = document.getElementById('input-menu-harga');
    const inputQty = document.getElementById('input-qty');
    const orderTotalPrice = document.getElementById('order-total-price');
    const customAlert = document.getElementById('custom-alert');
    const alertTitle = document.getElementById('alert-title');
    const alertMessage = document.getElementById('alert-message');

    // Scroll shadow header effect
    window.addEventListener('scroll', () => {
        if (window.scrollY > 20) {
            mainHeader.classList.add('scrolled');
        } else {
            mainHeader.classList.remove('scrolled');
        }
    });

    // Membuka modal form pesanan instan
    function openOrderModal(idMenu, namaMenu, harga) {
        selectedMenuName.textContent = namaMenu;
        selectedMenuPriceText.textContent = `Rp ${harga.toLocaleString('id-ID')}`;
        inputMenuId.value = idMenu;
        inputMenuHarga.value = harga;

        // Set default qty = 1
        inputQty.value = 1;
        updateSubtotal();

        // Animasi membuka modal
        orderModal.classList.add('open');
    }

    // Menutup modal form pesanan
    function closeOrderModal() {
        orderModal.classList.remove('open');
    }

    // Mengubah porsi menu (+ / -)
    function adjustQty(amount) {
        let currentQty = parseInt(inputQty.value) || 1;
        currentQty += amount;
        if (currentQty < 1) currentQty = 1;
        inputQty.value = currentQty;
        updateSubtotal();
    }

    // Memperbarui subtotal pembayaran di form
    function updateSubtotal() {
        const harga = parseInt(inputMenuHarga.value) || 0;
        const qty = parseInt(inputQty.value) || 1;
        const subtotal = harga * qty;
        orderTotalPrice.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
    }

    // Filter Kategori Hidangan
    function filterMenu(category) {
        const buttons = document.querySelectorAll('.menu-filter-btn');
        buttons.forEach(btn => {
            if (btn.getAttribute('data-category') === category) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        const items = document.querySelectorAll('.menu-item');
        items.forEach(item => {
            if (category === 'semua' || item.getAttribute('data-category') === category) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    // Menampilkan Custom Alert Box dialog
    function showCustomAlert(title, message) {
        alertTitle.textContent = title;
        alertMessage.innerHTML = message;
        customAlert.classList.add('open');
    }

    // Menutup Custom Alert Box dialog
    function closeAlert() {
        customAlert.classList.remove('open');
    }

    // Memperbarui navigasi active state saat scroll
    const sections = document.querySelectorAll('section');
    const navLinks = document.querySelectorAll('.nav-link');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (window.scrollY >= (sectionTop - 150)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').slice(1) === current) {
                link.classList.add('active');
            }
        });
    });
</script>
</body>
</html>

