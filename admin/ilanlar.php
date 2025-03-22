<?php 
include 'header.php';

// İlan silme işlemi
if (isset($_GET['sil']) && isset($_GET['id'])) {
    $ilan_id = (int)$_GET['id'];
    
    try {
        // Önce ilan fotoğrafını sil
        $stmt = $db->prepare("SELECT foto FROM ilanlar WHERE id = ?");
        $stmt->execute([$ilan_id]);
        $foto = $stmt->fetchColumn();
        
        if ($foto && file_exists('../' . $foto)) {
            unlink('../' . $foto);
        }
        
        // İlanı sil
        $stmt = $db->prepare("DELETE FROM ilanlar WHERE id = ?");
        $stmt->execute([$ilan_id]);
        
        setFlashMessage('success', 'İlan başarıyla silindi.');
        redirect('ilanlar.php');
    } catch(PDOException $e) {
        setFlashMessage('error', 'İlan silinirken bir hata oluştu.');
    }
}

// Durum değiştirme işlemi
if (isset($_GET['durum']) && isset($_GET['id'])) {
    $ilan_id = (int)$_GET['id'];
    $yeni_durum = $_GET['durum'] === 'aktif' ? 'aktif' : 'pasif';
    
    try {
        $stmt = $db->prepare("UPDATE ilanlar SET status = ? WHERE id = ?");
        $stmt->execute([$yeni_durum, $ilan_id]);
        
        setFlashMessage('success', 'İlan durumu başarıyla güncellendi.');
        redirect('ilanlar.php');
    } catch(PDOException $e) {
        setFlashMessage('error', 'İlan durumu güncellenirken bir hata oluştu.');
    }
}

try {
    // İlanları getir
    $stmt = $db->prepare("
        SELECT i.*, u.ad, u.soyad 
        FROM ilanlar i 
        JOIN users u ON i.user_id = u.id 
        ORDER BY i.created_at DESC
    ");
    $stmt->execute();
    $ilanlar = $stmt->fetchAll();
    
} catch(PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">İlanlar</h2>
        <a href="ilan_ekle.php" 
           class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
            <i class="fas fa-plus mr-2"></i>
            Yeni İlan
        </a>
    </div>

    <!-- İlanlar Tablosu -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <?php if (empty($ilanlar)): ?>
            <div class="p-6 text-center text-gray-500">
                Henüz ilan bulunmuyor.
            </div>
        <?php else: ?>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fotoğraf
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İlan Bilgileri
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Konum
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fiyat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Durum
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($ilanlar as $ilan): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($ilan['foto']): ?>
                                    <img src="<?php echo '../' . htmlspecialchars($ilan['foto']); ?>" 
                                         alt="<?php echo htmlspecialchars($ilan['ilan_basligi']); ?>"
                                         class="h-20 w-20 object-cover rounded">
                                <?php else: ?>
                                    <div class="h-20 w-20 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($ilan['ilan_basligi']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo number_format($ilan['metrekare']); ?> m²
                                </div>
                                <div class="text-sm text-gray-500">
                                    Ada: <?php echo htmlspecialchars($ilan['ada']); ?> / 
                                    Parsel: <?php echo htmlspecialchars($ilan['parsel']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo htmlspecialchars($ilan['ad'] . ' ' . $ilan['soyad']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    <?php echo htmlspecialchars($ilan['il']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo htmlspecialchars($ilan['ilce']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo htmlspecialchars($ilan['mahalle']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo number_format($ilan['fiyat'], 2, ',', '.'); ?> ₺
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?php echo number_format($ilan['fiyat'] / $ilan['metrekare'], 2, ',', '.'); ?> ₺/m²
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $ilan['status'] === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $ilan['status'] === 'aktif' ? 'Aktif' : 'Pasif'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="../ilan_detay.php?id=<?php echo $ilan['id']; ?>" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    Görüntüle
                                </a>
                                <a href="ilan_duzenle.php?id=<?php echo $ilan['id']; ?>" 
                                   class="text-orange-600 hover:text-orange-900 mr-3">
                                    Düzenle
                                </a>
                                <?php if ($ilan['status'] === 'aktif'): ?>
                                    <a href="?durum=pasif&id=<?php echo $ilan['id']; ?>" 
                                       class="text-yellow-600 hover:text-yellow-900 mr-3">
                                        Pasife Al
                                    </a>
                                <?php else: ?>
                                    <a href="?durum=aktif&id=<?php echo $ilan['id']; ?>" 
                                       class="text-green-600 hover:text-green-900 mr-3">
                                        Aktife Al
                                    </a>
                                <?php endif; ?>
                                <a href="?sil=1&id=<?php echo $ilan['id']; ?>" 
                                   onclick="return confirm('Bu ilanı silmek istediğinizden emin misiniz?')"
                                   class="text-red-600 hover:text-red-900">
                                    Sil
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>