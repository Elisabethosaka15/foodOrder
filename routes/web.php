<?php

$page =
    $_GET['page']
    ?? '';

    

switch ($page) {
    case 'register':
        require
            'views/auth/register.php';
        break;

    case 'admin':
        require
            './views/admin/dasboard.php';
        break;

    case 'user':
        require
            './views/user/dasboard.php';
        break;
    case 'login':
        require
            './views/auth/login.php';
        break;

    default:
        require
            'views/landing.php';
}
