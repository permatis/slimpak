<?php namespace Slimpak;

class Session {

    public static function flash()
    {
        return (isset($_SESSION['slim.flash']) ? $_SESSION['slim.flash'] : '');
    }
}