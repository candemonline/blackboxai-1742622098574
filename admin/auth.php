<?php
require_once '../config.php';

// Admin veya danışman değilse ana sayfaya yönlendir
if (!isLoggedIn()) {
    setFlashMessage('error', 'Lütfen önce giriş yapın.');
    redirect('/login.php');
}

if (!isAdmin() && !isDanisman()) {
    setFlashMessage('error', 'Bu sayfaya erişim yetkiniz yok.');
    redirect('/');
}

// Admin menü öğelerini getir
function getAdminMenuItems() {
    $menu = [
        [
            'title' => 'Gösterge Paneli',
            'url' => '/admin/index.php',
            'icon' => 'fas fa-tachometer-alt'
        ]
    ];

    if (isAdmin()) {
        $menu = array_merge($menu, [
            [
                'title' => 'Danışmanlar',
                'url' => '/admin/danismanlar.php',
                'icon' => 'fas fa-users'
            ],
            [
                'title' => 'Talepler',
                'url' => '/admin/talepler.php',
                'icon' => 'fas fa-clipboard-list'
            ]
        ]);
    }

    $menu = array_merge($menu, [
        [
            'title' => 'İlanlar',
            'url' => '/admin/ilanlar.php',
            'icon' => 'fas fa-home'
        ],
        [
            'title' => 'Blog',
            'url' => '/admin/blog.php',
            'icon' => 'fas fa-newspaper'
        ]
    ]);

    return $menu;
}

// Aktif menü öğesini belirle
function isActiveMenuItem($url) {
    return $_SERVER['SCRIPT_NAME'] === $url ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-orange-100';
}
