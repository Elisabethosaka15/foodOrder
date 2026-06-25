<?php

$auth = new AuthController();
$registerResult = $auth->register();

if ($registerResult['success']) {
    echo "
    <script>
    alert('Registrasi Berhasil');
    location='index.php?page=login';
    </script>
    ";
    exit;
}

$oldNama = $registerResult['data']['nama'] ?? '';
$oldUsername = $registerResult['data']['username'] ?? '';
$oldEmail = $registerResult['data']['email'] ?? '';
$errorMessage = $registerResult['message'];
?>

<?php include 'views/layouts/header.php'; ?>

<div class="auth-wrapper">
    <div class="auth-box">
        <div class="card">
            <h2 class="auth-title">Register FoodOrder</h2>
            <div class="auth-subtitle">Silakan buat akun Anda</div>

            <?php if ($errorMessage) : ?>
                <div class="auth-alert auth-alert-error">
                    <?= $errorMessage ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="auth-form-group">
                    <label>Nama Lengkap</label>
                    <input
                        type="text"
                        name="nama"
                        placeholder="Masukkan nama"
                        class="form-input"
                        value="<?= $oldNama ?>"
                        required>
                </div>
                <div class="auth-form-group">
                    <label>Username</label>
                    <input
                        type="text"
                        name="username"
                        placeholder="Masukkan username"
                        class="form-input"
                        value="<?= $oldUsername ?>"
                        required>
                </div>
                <div class="auth-form-group">
                    <label>Email</label>
                    <input
                        type="email"
                        name="email"
                        placeholder="Masukkan email"
                        class="form-input"
                        value="<?= $oldEmail ?>"
                        required>
                </div>

                <div class="auth-form-group">
                    <label>Password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        class="form-input"
                        required>
                </div>

                <button
                    type="submit"
                    name="register"
                    class="btn-primary">
                    <i class="fa-solid fa-right-to-bracket"></i> Register
                </button>
            </form>

            <div class="auth-footer-text">
                <p>
                    Sudah punya akun?
                </p>
                <a href="index.php?page=login">Login</a>
            </div>
        </div>
    </div>
</div>


<?php include 'views/layouts/footer.php'; ?>