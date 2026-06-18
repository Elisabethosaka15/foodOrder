<?php

Session::check();
Auth::admin();
?>

<?php include 'views/layouts/header.php'; ?>

<div class="container mt-5">

<h2>
Dashboard Admin
</h2>

<p>
Selamat Datang
<?= $_SESSION['nama']; ?>
</p>

<a
href="logout.php"
class="btn btn-danger">
Logout
</a>

</div>

<?php include 'views/layouts/footer.php'; ?>

