<?php 
require_once 'header.php';

try {
    // İstatistikleri getir
    $stats = [
        'bekleyen_talepler' => $db->query("SELECT COUNT(*) FROM arazi_talepleri WHERE status = 'beklemede'")->fetchColumn(),
        'aktif_ilanlar' => $db->query("SELECT COUNT(*) FROM ilanlar WHERE status = 'aktif'")->fetchColumn(),
        'toplam_danismanlar' => $db->query("SELECT COUNT(*) FROM users WHERE user_type = 'danisman' AND status = 'active'")->fetchColumn(),
        'bekleyen_danismanlar' => $db->query("SELECT COUNT(*) FROM users WHERE user_type = 'danisman' AND status = 'pending'")->fetchColumn(),
        'blog_yazilari' => $db->query("SELECT COUNT(*) FROM blog")->fetchColumn()
    ];

    // Son ilanları getir
    $stmt = $db->query("
        SELECT i.*, u.ad, u.soyad 
        FROM ilanlar i 
        JOIN users u ON i.user_id = u.id 
        WHERE i.status = 'aktif' 
        ORDER BY i.created_at DESC 
        LIMIT 5
    ");
    $son_ilanlar = $stmt->fetchAll();

    // Son talepleri getir
    $stmt = $db->query("
        SELECT t.*, u.ad, u.soyad 
        FROM arazi_talepleri t 
        JOIN users u ON t.user_id = u.id 
        ORDER BY t.created_at DESC 
        LIMIT 5
    ");
    $son_talepler = $stmt->fetchAll();

    // Son blog yazılarını getir
    $stmt = $db->query("
        SELECT b.*, u.ad, u.soyad 
        FROM blog b 
        JOIN users u ON b.user_id = u.id 
        ORDER BY b.created_at DESC 
        LIMIT 5
    ");
    $son_blog_yazilari = $stmt->fetchAll();

} catch(PDOException $e) {
    die("Bir hata oluştu: " . $e->getMessage());
}
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- İstatistik Kartları -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-500">
                <i class="fas fa-clipboard-list text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Bekleyen Talepler</p>
                <p class="text-lg font-semibold"><?php echo $stats['bekleyen_talepler']; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                <i class="fas fa-home text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Aktif İlanlar</p>
                <p class="text-lg font-semibold"><?php echo $stats['aktif_ilanlar']; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-500">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Aktif Danışmanlar</p>
                <p class="text-lg font-semibold"><?php echo $stats['toplam_danismanlar']; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-500">
                <i class="fas fa-newspaper text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm text-gray-500">Blog Yazıları</p>
                <p class="text-lg font-semibold"><?php echo $stats['blog_yazilari']; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Son İlanlar -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Son İlanlar</h3>
            <a href="ilanlar.php" class="text-orange-500 hover:text-orange-600">Tümünü Gör</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Başlık</th>
                        <th class="text-left py-2">Fiyat</th>
                        <th class="text-left py-2">Danışman</th>
                        <th class="text-left py-2">Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($son_ilanlar as $ilan): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2"><?php echo e($ilan['ilan_basligi']); ?></td>
                            <td class="py-2"><?php echo number_format($ilan['fiyat'], 2, ',', '.'); ?> ₺</td>
                            <td class="py-2"><?php echo e($ilan['ad'] . ' ' . $ilan['soyad']); ?></td>
                            <td class="py-2"><?php echo formatDate($ilan['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Son Talepler -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Son Talepler</h3>
            <a href="talepler.php" class="text-orange-500 hover:text-orange-600">Tümünü Gör</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Kullanıcı</th>
                        <th class="text-left py-2">Konum</th>
                        <th class="text-left py-2">Durum</th>
                        <th class="text-left py-2">Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($son_talepler as $talep): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2"><?php echo e($talep['ad'] . ' ' . $talep['soyad']); ?></td>
                            <td class="py-2"><?php echo e($talep['il'] . '/' . $talep['ilce']); ?></td>
                            <td class="py-2">
                                <span class="px-2 py-1 rounded text-xs 
                                    <?php echo $talep['status'] === 'beklemede' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($talep['status'] === 'teklif_verildi' ? 'bg-green-100 text-green-800' : 
                                        'bg-red-100 text-red-800'); ?>">
                                    <?php echo ucfirst($talep['status']); ?>
                                </span>
                            </td>
                            <td class="py-2"><?php echo formatDate($talep['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Son Blog Yazıları -->
<div class="mt-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Son Blog Yazıları</h3>
            <a href="blog.php" class="text-orange-500 hover:text-orange-600">Tümünü Gör</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($son_blog_yazilari as $blog): ?>
                <div class="border rounded-lg overflow-hidden">
                    <?php if ($blog['gorsel']): ?>
                        <img src="<?php echo e($blog['gorsel']); ?>" 
                             alt="<?php echo e($blog['baslik']); ?>" 
                             class="w-full h-48 object-cover">
                    <?php endif; ?>
                    <div class="p-4">
                        <h4 class="font-semibold mb-2"><?php echo e($blog['baslik']); ?></h4>
                        <p class="text-sm text-gray-600 mb-2">
                            <?php echo e($blog['ad'] . ' ' . $blog['soyad']); ?> | 
                            <?php echo formatDate($blog['created_at']); ?>
                        </p>
                        <a href="blog_duzenle.php?id=<?php echo $blog['id']; ?>" 
                           class="text-orange-500 hover:text-orange-600 text-sm">
                            Düzenle
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>