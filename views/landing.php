<?php include './views/layouts/header.php';

$auth = new AuthController();
$pesan = new PesananController();
$menu = new MenuController();


// if($pesan->pesanan()){
// }


if ($auth->logout()) {
    echo "
      <script>
    alert('Login Berhasil');
    </script>
    ";
}

?>

<!-- HEADER & NAVBAR (Koneksi database: 'users' untuk data user login) -->
<header class="header" id="main-header">
    <div class="container">
        <!-- Brand Logo -->
        <div class="logo">
            <span class="logo-icon">
                <i class="fa-solid fa-utensils"></i>
            </span>
            <span class="logo-text">Rasa<span>Nusantara</span></span>
        </div>

        <!-- Navigasi -->
        <nav class="nav">
            <a href="#beranda" class="nav-link active" id="nav-beranda">Beranda</a>
            <a href="#menu" class="nav-link" id="nav-menu">Daftar Menu</a>
        </nav>

        <!-- Profil User / login -->
        <div class="user-action">
            <!-- PHP INTEGRATION NOTE: 
                     Gunakan session PHP ($_SESSION['user']) untuk memeriksa status login.
                     Jika ada session, ganti tombol login dengan nama user dan opsi logout.
                -->
            <?php if ($_SESSION['nama']) : ?>
                <div class="user-wrap">
                    <span class="user-label">Halo, <?= $_SESSION['nama'] ?></span>

                    <form method="post">
                        <button name="logout" class="btn btn-danger">
                            Logout
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

<!-- CONTENT UTAMA -->
<main>

    <!-- Hero Section -->
    <section id="beranda" class="hero">
        <div class="container">
            <div class="hero-grid">

                <!-- Informasi Text Hero -->
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

    <!-- Menu Section (Hubungan dengan tabel database: 'menu') -->
    <section id="menu" class="menu-section">
        <div class="container">

            <div class="menu-header">
                <div class="menu-title-block">
                    <h2>Daftar Menu Hidangan</h2>
                    <p>Sistem Pemesanan Langsung terintegrasi dengan tabel database</p>
                </div>

                <!-- Filter Kategori Native -->
                <div class="filter-container">
                    <button onclick="filterMenu('semua')" class="menu-filter-btn active" data-category="semua">Semua</button>
                    <button onclick="filterMenu('Makanan')" class="menu-filter-btn" data-category="Makanan">Makanan</button>
                    <button onclick="filterMenu('Minuman')" class="menu-filter-btn" data-category="Minuman">Minuman</button>
                </div>
            </div>

            <!-- PHP INTEGRATION NOTE: 
                     Di dalam backend MVC Anda, buat model untuk mengambil seluruh data dari tabel `menu`.
                     Contoh query: "SELECT * FROM menu"
                     Kemudian lakukan perulangan PHP: foreach ($menu_items as $item) { ... }
                -->
            <div class="menu-grid" id="menu-container">
                <?php $result = $menu->getMenu()?>

                <?php while ($row= $result->fetch_assoc()) { ?>
                    <!-- Item Menu 1 (Makanan) -->
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
                        '<?= $row['nama_menu']; ?>',
                        <?= $row['harga']; ?>
                    )"
                                    class="btn-order">
                                    Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
                <!-- Item Menu 2 (Makanan)
                <div class="menu-item" data-category="Makanan" data-id="2">
                    <div class="item-media">
                        <i class="fa-solid fa-pepper-hot"></i>
                        <span class="item-db-id">ID Menu: 2</span>
                    </div>
                    <div class="item-content">
                        <h3 class="item-title">Ayam Penyet Sambal Korek</h3>
                        <p class="item-desc">Ayam goreng renyah bumbu kuning yang dipenyet dengan siraman sambal bawang korek pedas dan lalapan segar.</p>
                        <div class="item-action-area">
                            <span class="item-price">Rp 28.000</span>
                            <button onclick="openOrderModal(2, 'Ayam Penyet Sambal Korek', 28000)" class="btn-order">
                                Pesan Sekarang
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item Menu 3 (Minuman) -->
                <div class="menu-item" data-category="Minuman" data-id="3">
                    <div class="item-media">
                        <i class="fa-solid fa-glass-water"></i>
                        <span class="item-db-id">ID Menu: 3</span>
                    </div>
                    <div class="item-content">
                        <h3 class="item-title">Es Teh Manis Selasih</h3>
                        <p class="item-desc">Minuman teh seduh wangi melati segar manis dengan sensasi biji selasih berkhasiat tinggi.</p>
                        <div class="item-action-area">
                            <span class="item-price">Rp 7.000</span>
                            <button onclick="openOrderModal(3, 'Es Teh Manis Selasih', 7000)" class="btn-order">
                                Pesan Sekarang
                            </button>
                        </div>
                    </div>
                </div> 

            </div>
        </div>
    </section>

</main>

<!-- MODAL POPUP FORM PEMESANAN (Hubungan tabel: 'pesanan', 'detail_pesanan', dan 'pembayaran') -->
<div id="order-modal" class="modal-overlay">
    <div class="modal-card" id="order-card">

        <!-- Header Modal -->
        <div class="modal-header">
            <div class="modal-header-text">
                <h3><i class="fa-solid fa-file-invoice" style="color: var(--primary);"></i> Form Pemesanan</h3>
                <p>Memasukkan data ke tabel: <span>pesanan, detail_pesanan, pembayaran</span></p>
            </div>
            <button onclick="closeOrderModal()" class="btn-close-modal">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Item Terpilih -->
        <div class="selected-item-box">
            <span>Menu Pilihan Anda</span>
            <div class="selected-item-detail">
                <h4 id="selected-menu-name" class="selected-item-name">Nama Menu</h4>
                <span id="selected-menu-price-text" class="selected-item-price">Rp 0</span>
            </div>
        </div>

        <!-- Form input pemesanan -->
        <form id="direct-order-form" method="post">
            <!-- Input tersembunyi sebagai acuan pengiriman data ke server controller PHP -->
            <input type="hidden" id="input-menu-id" name="id_menu">
            <input type="hidden" id="input-menu-harga">

            <!-- Qty (Jumlah Pembelian) -> Disimpan ke tabel detail_pesanan.jumlah -->
            <div class="form-group">
                <label class="form-label">Jumlah Porsi (Tabel: detail_pesanan)</label>
                <div class="qty-control">
                    <button type="button" onclick="adjustQty(-1)" class="btn-qty">-</button>
                    <input type="number" id="input-qty" name="jumlah" value="1" min="1" class="input-qty-number" readonly>
                    <button type="button" onclick="adjustQty(1)" class="btn-qty">+</button>
                </div>
            </div>

            <!-- Nama Lengkap Pemesan -> Disimpan ke tabel users (atau gunakan Session ID) -->
            <div class="form-group">
                <label class="form-label">Nama Lengkap Pelanggan (Tabel: users)</label>
                <input type="text" id="customer-name" name="nama_pelanggan" placeholder="Masukkan nama lengkap Anda" class="form-input" required>
            </div>

            <!-- Metode Pembayaran -> Disimpan ke tabel pembayaran.metode_pembayaran -->
            <div class="form-group">
                <label class="form-label">Metode Pembayaran (Tabel: pembayaran)</label>
                <select id="payment-method" name="metode_pembayaran" class="form-input" style="cursor: pointer;">
                    <option value="Transfer Bank">Transfer Bank / Virtual Account</option>
                    <option value="E-Wallet">E-Wallet (OVO / GoPay / Dana)</option>
                    <option value="Tunai">Bayar Tunai di Kasir (COD)</option>
                </select>
            </div>

            <!-- Total Pembayaran (Harga * Qty) -> Disimpan ke tabel pesanan.total_harga -->
            <div class="total-payment-row">
                <span>Total Pembayaran:</span>
                <span id="order-total-price">Rp 0</span>
            </div>

            <!-- Tombol Submit Form -->
            <!-- PHP INTEGRATION NOTE:
                     Ketika diklik, data ini dikirimkan ke Controller (POST). Di Controller Anda:
                     1. Ambil/buat data customer di tabel `users`.
                     2. Tambah data di `pesanan` -> Simpan nilai total harga dan user id. Ambil ID pesanan baru.
                     3. Tambah data di `detail_pesanan` -> Masukkan id_pesanan, id_menu, jumlah porsi.
                     4. Tambah data di `pembayaran` -> Catat metode pembayaran pelanggan.
                -->
            <button type="submit" class="btn-submit-order" name="pesan">
                <i class="fa-solid fa-circle-check"></i> Kirim & Konfirmasi Pesanan
            </button>
        </form>

    </div>
</div>

<!-- FOOTER RINKAS -->
<footer class="footer">
    <div class="container">
        <p class="footer-main-text">RasaNusantara - Landing Page PHP Native MVC Framework</p>
        <p>&copy; 2026 RasaNusantara. Integrasi tabel database: <code>menu, users, pesanan, detail_pesanan, pembayaran</code></p>
    </div>
</footer>

<!-- CUSTOM ALERT DIALOG POPUP (Desain Premium Tanpa Alert Bawaan Browser) -->
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

    // Mengirim dan memproses Form (Simulasi alur database MVC)
    function handleDirectOrder(event) {
        event.preventDefault();

        const menuName = selectedMenuName.textContent;
        const qty = inputQty.value;
        const customerName = document.getElementById('customer-name').value;
        const paymentMethod = document.getElementById('payment-method').value;
        const totalPrice = orderTotalPrice.textContent;

        const dbSimulationLog = `<strong>Selamat Kak ${customerName}!</strong> Pesanan Anda berhasil dicatat.<br><br>`;

        closeOrderModal();

        document.getElementById('customer-name').value = '';

        setTimeout(() => {
            showCustomAlert("Transaksi Sukses!", dbSimulationLog);
        }, 300);
    }

    // Filter Kategori Hidangan
    function filterMenu(category) {
        // Perbarui class active pada tombol
        const buttons = document.querySelectorAll('.menu-filter-btn');
        buttons.forEach(btn => {
            if (btn.getAttribute('data-category') === category) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Saring card list menu
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