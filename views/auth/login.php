<?php

$auth = new AuthController();

if ($auth->login()) {
    if ($_SESSION['role'] == 'admin') {
        header(
            "Location:index.php?page=admin"
        );
    } else {
        header(
            "Location:index.php"
        );
    }

    exit;
}

include
    'views/layouts/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-box">
        <div class="card">
            <h2 class="auth-title">Login FoodOrder</h2>
            <div class="auth-subtitle">Silakan masuk untuk melanjutkan pesanan Anda</div>

            <form method="POST" class="auth-form">
                <div class="auth-form-group">
                    <label>Username</label>
                    <input
                        type="text"
                        name="username"
                        placeholder="Masukkan username"
                        class="form-input"
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
                    name="login"
                    class="btn-primary">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
                </button>
            </form>

            <div class="auth-footer-text">
                <p>
                    Belum punya akun?
                </p>
                <a href="index.php?page=register">Register</a>
            </div>
        </div>
    </div>
</div>

<?php
include
    'views/layouts/footer.php';
?>