<?php

$auth =
new AuthController();

if($auth->register())
{
    echo "
    <script>
    alert('Registrasi Berhasil');
    location='index.php?page=login';
    </script>
    ";
}
?>

<?php include 'views/layouts/header.php'; ?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card">

<div class="card-body">

<h2 class="title-food text-center">
Register
</h2>

<form method="POST">

<input
type="text"
name="nama"
class="form-control mb-3"
placeholder="Nama"
required>

<input
type="text"
name="username"
class="form-control mb-3"
placeholder="Username"
required>

<input
type="email"
name="email"
class="form-control mb-3"
placeholder="Email"
required>

<input
type="password"
name="password"
class="form-control mb-3"
placeholder="Password"
required>

<button
name="register"
class="btn btn-food w-100">
Daftar
</button>

</form>

</div>
</div>

</div>
</div>
</div>

<?php include 'views/layouts/footer.php'; ?>

