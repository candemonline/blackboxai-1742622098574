<?php
require_once 'config.php';

// Oturumu sonlandır
session_destroy();

// Çerezleri temizle
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Ana sayfaya yönlendir
redirect('/');
?>