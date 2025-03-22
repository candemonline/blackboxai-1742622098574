<?php
require_once '../config.php';
require_once 'auth.php';

header('Content-Type: application/json');

// ID kontrolü
$danisman_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$danisman_id) {
    echo json_encode(['error' => 'Geçersiz danışman ID']);
    exit;
}

try {
    // Danışman bilgilerini getir
    $stmt = $db->prepare("
        SELECT id, ad, soyad, email, telefon, created_at, myk_belgesi, vergi_levhasi 
        FROM users 
        WHERE id = ? AND user_type = 'danisman'
    ");
    $stmt->execute([$danisman_id]);
    $danisman = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$danisman) {
        echo json_encode(['error' => 'Danışman bulunamadı']);
        exit;
    }

    // Tarihi formatla
    $danisman['created_at'] = date('d.m.Y H:i', strtotime($danisman['created_at']));

    echo json_encode($danisman);

} catch(PDOException $e) {
    echo json_encode(['error' => 'Veritabanı hatası']);
}