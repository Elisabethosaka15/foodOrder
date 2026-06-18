<?php

$auth = new AuthController();

if($auth->login())
{
    if($_SESSION['role'] == 'admin')
    {
        header(
        "Location:index.php?page=admin"
        );
    }
    else
    {
        header(
        "Location:index.php?page=user"
        );
    }

    exit;
}

include
'views/layouts/header.php';
?>

<div class="login-box">

    <div class="card">

        <h2 align="center">
            Login FoodOrder
        </h2>

        <br>

        <form method="POST">

            <input
            type="text"
            name="username"
            placeholder="Username"
            class="form-control"
            required>

            <input
            type="password"
            name="password"
            placeholder="Password"
            class="form-control"
            required>

            <button
            type="submit"
            name="login"
            class="btn btn-primary">

                Login

            </button>

        </form>

        <br>

        <p align="center">

            Belum punya akun?

            <a href="index.php?page=register">

                Register

            </a>

        </p>

    </div>

</div>

<?php
include
'views/layouts/footer.php';
?>

