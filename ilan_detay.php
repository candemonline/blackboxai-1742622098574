<?php 
require_once 'header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $stmt = $db->prepare("
        SELECT i.*, u.ad, u.soyad, u.telefon, u.email 
        FROM ilanlar i 
        JOIN users u ON i.user_id = u.id 
        WHERE i.id = ? AND i.status = 'aktif'
    ");
    $stmt->execute([$id]);
    $ilan = $stmt->fetch();

    if (!$ilan) {
        redirect('ilanlar.php');
    }

    // Benzer ilanları getir
    $stmt = $db->prepare("
        SELECT id, ilan_basligi, fiyat, metrekare, il, ilce, foto 
        FROM ilanlar 
        WHERE id != ? 
            AND il = ? 
            AND status = 'aktif' 
        ORDER BY created_at DESC 
        LIMIT 3
    ");
    $stmt->execute([$id, $ilan['il']]);
    $benzer_ilanlar = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Bir hata oluştu.");
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Ana İçerik -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <?php if ($ilan['foto']): ?>
                    <img src="<?php echo e($ilan['foto']); ?>" 
                         alt="<?php echo e($ilan['ilan_basligi']); ?>" 
                         class="w-full h-96 object-cover">
                <?php endif; ?>

                <div class="p-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">
                        <?php echo e($ilan['ilan_basligi']); ?>
                    </h1>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-map-marker-alt w-6"></i>
                            <span><?php echo e($ilan['il'] . ' / ' . $ilan['ilce']); ?></span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-ruler-combined w-6"></i>
                            <span><?php echo number_format($ilan['metrekare']); ?> m²</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-map w-6"></i>
                            <span>Ada: <?php echo e($ilan['ada']); ?></span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-map-signs w-6"></i>
                            <span>Parsel: <?php echo e($ilan['parsel']); ?></span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-calendar w-6"></i>
                            <span><?php echo formatDateTime($ilan['created_at']); ?></span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-user w-6"></i>
                            <span><?php echo e($ilan['ad'] . ' ' . $ilan['soyad']); ?></span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">Konum Bilgileri</h2>
                        <div class="prose prose-lg max-w-none">
                            <p class="text-gray-600">
                                <?php echo e($ilan['il'] . ' ili, ' . $ilan['ilce'] . ' ilçesi, ' . $ilan['mahalle'] . ' mahallesi'); ?>
                            </p>
                            <p class="text-gray-600">
                                Ada: <?php echo e($ilan['ada']); ?>, Parsel: <?php echo e($ilan['parsel']); ?>
                            </p>
                            <?php if ($ilan['tkgm_link']): ?>
                                <p class="mt-4">
                                    <a href="<?php echo e($ilan['tkgm_link']); ?>" 
                                       target="_blank"
                                       class="inline-flex items-center text-orange-500 hover:text-orange-600">
                                        <i class="fas fa-external-link-alt mr-2"></i>
                                        TKGM Parsel Sorgu
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-8 mt-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">İletişim Bilgileri</h2>
                        <div class="space-y-4">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-user w-6"></i>
                                <span><?php echo e($ilan['ad'] . ' ' . $ilan['soyad']); ?></span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-phone w-6"></i>
                                <a href="tel:<?php echo e($ilan['telefon']); ?>" class="hover:text-orange-500">
                                    <?php echo e($ilan['telefon']); ?>
                                </a>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-envelope w-6"></i>
                                <a href="mailto:<?php echo e($ilan['email']); ?>" class="hover:text-orange-500">
                                    <?php echo e($ilan['email']); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yan Panel -->
        <div class="lg:col-span-1">
            <!-- Fiyat Kartı -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-500 mb-2">
                        <?php echo number_format($ilan['fiyat'], 2, ',', '.'); ?> ₺
                    </div>
                    <div class="text-gray-500">
                        <?php echo number_format($ilan['fiyat'] / $ilan['metrekare'], 2, ',', '.'); ?> ₺/m²
                    </div>
                </div>
                <div class="mt-6 space-y-4">
                    <a href="tel:<?php echo e($ilan['telefon']); ?>" 
                       class="flex items-center justify-center w-full px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600">
                        <i class="fas fa-phone mr-2"></i>
                        Ara
                    </a>
                    <a href="https://wa.me/<?php echo e(str_replace([' ', '(', ')', '-'], '', $ilan['telefon'])); ?>?text=<?php echo urlencode(SITE_URL . '/ilan_detay.php?id=' . $ilan['id'] . ' ilanınız hakkında bilgi almak istiyorum.'); ?>" 
                       target="_blank"
                       class="flex items-center justify-center w-full px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-500 hover:bg-green-600">
                        <i class="fab fa-whatsapp mr-2"></i>
                        WhatsApp
                    </a>
                </div>
            </div>

            <!-- Benzer İlanlar -->
            <?php if (!empty($benzer_ilanlar)): ?>
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Benzer İlanlar</h3>
                    <div class="space-y-4">
                        <?php foreach ($benzer_ilanlar as $benzer_ilan): ?>
                            <a href="ilan_detay.php?id=<?php echo $benzer_ilan['id']; ?>" 
                               class="flex items-center space-x-4 group">
                                <?php if ($benzer_ilan['foto']): ?>
                                    <img src="<?php echo e($benzer_ilan['foto']); ?>" 
                                         alt="<?php echo e($benzer_ilan['ilan_basligi']); ?>" 
                                         class="w-20 h-20 object-cover rounded">
                                <?php else: ?>
                                    <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-2xl text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1">
                                    <h4 class="text-gray-900 group-hover:text-orange-500 transition-colors duration-200">
                                        <?php echo e($benzer_ilan['ilan_basligi']); ?>
                                    </h4>
                                    <div class="text-sm text-gray-500">
                                        <?php echo number_format($benzer_ilan['fiyat'], 2, ',', '.'); ?> ₺
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo e($benzer_ilan['il'] . ' / ' . $benzer_ilan['ilce']); ?>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>