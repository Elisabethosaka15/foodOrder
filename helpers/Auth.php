<?php

class Auth
{
    public static function admin()
    {
        if(
            $_SESSION['role']
            != 'admin'
        )
        {
            header(
                "Location:index.php?page=login"
            );
            exit;
        }
    }

    public static function user()
    {
        if(
            $_SESSION['role']
            != 'user'
        )
        {
            header(
                "Location:index.php?page=login"
            );
            exit;
        }
    }
}

