<?php

Session::check();
Auth::user();
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

<a
href="logout.php"
class="btn btn-danger">
Logout
</a>

</div>

<?php include 'views/layouts/footer.php'; ?>

