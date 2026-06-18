<?php

require_once "models/User.php";

class AuthController
{
    private $user;

    public function __construct()
    {
        $this->user =
        new User();
    }

    public function register()
    {
        if(isset($_POST['register']))
        {
            return
            $this->user->create(
                $_POST['nama'],
                $_POST['username'],
                $_POST['email'],
                $_POST['password']
            );
        }

        return false;
    }

    public function login()
    {
        if(isset($_POST['login']))
        {
            $user =
            $this->user
            ->getByUsername(
                $_POST['username']
            );

            if(
                $user &&
                password_verify(
                    $_POST['password'],
                    $user['password']
                )
            )
            {
                $_SESSION['user_id']
                    = $user['id'];

                $_SESSION['nama']
                    = $user['nama'];

                $_SESSION['role']
                    = $user['role'];

                return true;
            }
        }

        return false;
    }
}

