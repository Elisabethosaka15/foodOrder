<?php

$page =
$_GET['page']
?? 'login';

switch($page)
{
    case 'register':
        require
        'views/auth/register.php';
        break;

    case 'admin':
        require
        'views/admin/dashboard.php';
        break;

    case 'user':
        require
        './views/user/dasboard.php';
        break;

    default:
        require
        'views/auth/login.php';
}

