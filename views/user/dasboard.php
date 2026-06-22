<?php

Session::check();
Auth::user();
$auth = new AuthController();

if ($auth->logout()) {
    echo "
      <script>
    alert('Login Berhasil');
    </script>
    ";
}

?>

<?php include 'views/layouts/header.php'; ?>

<div class="container mt-5">

    <h2>
        Dashboard User
    </h2>

    <p>
        Selamat Datang
        <?= $_SESSION['nama']; ?>
    </p>

    <form method="post">
        <button name="logout" class="btn btn-danger">
            Logout
        </button>
    </form>

</div>

<?php include 'views/layouts/footer.php'; ?>