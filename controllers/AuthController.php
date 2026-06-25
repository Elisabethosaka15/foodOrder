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
        if (!isset($_POST['register'])) {
            return [
                'success' => false,
                'message' => null,
                'data' => []
            ];
        }

        $nama = trim($_POST['nama'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];

        if ($nama === '') {
            $errors[] = 'Nama lengkap harus diisi.';
        }

        if ($username === '') {
            $errors[] = 'Username harus diisi.';
        }

        if ($email === '') {
            $errors[] = 'Email harus diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid.';
        }

        if ($password === '') {
            $errors[] = 'Password harus diisi.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung minimal 1 angka.';
        }

        if ($this->user->getByUsername($username)) {
            $errors[] = 'Username sudah digunakan.';
        }

        if ($this->user->getByEmail($email)) {
            $errors[] = 'Email sudah terdaftar.';
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => implode('<br>', $errors),
                'data' => [
                    'nama' => htmlspecialchars($nama),
                    'username' => htmlspecialchars($username),
                    'email' => htmlspecialchars($email)
                ]
            ];
        }

        $created = $this->user->create(
            $nama,
            $username,
            $email,
            $password
        );

        if ($created) {
            return [
                'success' => true,
                'message' => 'Registrasi berhasil. Silakan login.',
                'data' => []
            ];
        }

        return [
            'success' => false,
            'message' => 'Terjadi kesalahan saat menambahkan pengguna.',
            'data' => [
                'nama' => htmlspecialchars($nama),
                'username' => htmlspecialchars($username),
                'email' => htmlspecialchars($email)
            ]
        ];
    }

    public function login()
    {
        if (isset($_POST['login'])) {
            $user =
                $this->user
                ->getByUsername(
                    $_POST['username']
                );

            if (
                $user &&
                password_verify(
                    $_POST['password'],
                    $user['password']
                )
            ) {
                $_SESSION['user_id']
                    = $user['id'];

                $_SESSION['nama']
                    = $user['nama'];
                $_SESSION['username']
                    = $user['username'];


                $_SESSION['role']
                    = $user['role'];

                return true;
            }
        }

        return false;
    }

    public function logout()
    {
        if (isset($_POST['logout'])) {

            session_destroy();

            header(
                "Location:index.php?page=login"
            );
            exit;
        }
    }
}
