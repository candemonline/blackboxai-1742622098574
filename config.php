<?php
session_start();

// Site ayarları
define('SITE_URL', 'http://localhost:8000');
define('SITE_TITLE', 'Arazi Değerleme');
define('UPLOADS_DIR', __DIR__ . '/uploads');

// Veritabanı bağlantısı
try {
    $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die('Veritabanı bağlantı hatası: ' . $e->getMessage());
}

// Flash mesajları
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// CSRF koruması
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

function validateCSRFToken() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// Oturum yönetimi
function isLoggedIn() {
    if (isset($_SESSION['user_id'])) {
        return true;
    }
    
    // Remember me kontrolü
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        
        $stmt = $GLOBALS['db']->prepare("SELECT * FROM users WHERE remember_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['user_name'] = $user['ad'] . ' ' . $user['soyad'];
            return true;
        }
    }
    
    return false;
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function isDanisman() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'danisman';
}

// Yönlendirme
function redirect($path) {
    header('Location: ' . SITE_URL . $path);
    exit;
}

// XSS koruması
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Tarih formatı
function formatDate($date) {
    return date('d.m.Y H:i', strtotime($date));
}