<?php 
include 'header.php';

// Blog silme işlemi
if (isset($_GET['sil']) && isset($_GET['id'])) {
    $blog_id = (int)$_GET['id'];
    
    try {
        // Önce blog yazısını getir
        $stmt = $db->prepare("SELECT gorsel FROM blog WHERE id = ?");
        $stmt->execute([$blog_id]);
        $blog = $stmt->fetch();
        
        // Görseli sil
        if ($blog && $blog['gorsel'] && file_exists('../' . $blog['gorsel'])) {
            unlink('../' . $blog['gorsel']);
        }
        
        // Blog yazısını sil
        $stmt = $db->prepare("DELETE FROM blog WHERE id = ?");
        $stmt->execute([$blog_id]);
        
        setFlashMessage('success', 'Blog yazısı başarıyla silindi.');
        redirect('blog.php');
    } catch(PDOException $e) {
        setFlashMessage('error', 'Blog yazısı silinirken bir hata oluştu.');
    }
}

try {
    // Blog yazılarını getir
    $stmt = $db->prepare("
        SELECT b.*, u.ad, u.soyad 
        FROM blog b 
        JOIN users u ON b.user_id = u.id 
        ORDER BY b.created_at DESC
    ");
    $stmt->execute();
    $blog_yazilari = $stmt->fetchAll();
    
} catch(PDOException $e) {
    setFlashMessage('error', 'Blog yazıları yüklenirken bir hata oluştu.');
}
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Blog Yazıları</h2>
        <a href="blog_ekle.php" 
           class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
            <i class="fas fa-plus mr-2"></i>
            Yeni Yazı Ekle
        </a>
    </div>

    <!-- Blog Yazıları Tablosu -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <?php if (empty($blog_yazilari)): ?>
            <div class="p-6 text-center text-gray-500">
                Henüz blog yazısı bulunmuyor.
            </div>
        <?php else: ?>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Görsel
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Başlık
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Yazar
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tarih
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($blog_yazilari as $blog): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($blog['gorsel']): ?>
                                    <img src="<?php echo htmlspecialchars($blog['gorsel']); ?>" 
                                         alt="<?php echo htmlspecialchars($blog['baslik']); ?>"
                                         class="h-12 w-12 object-cover rounded">
                                <?php else: ?>
                                    <div class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($blog['baslik']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo substr(strip_tags($blog['icerik']), 0, 100) . '...'; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($blog['ad'] . ' ' . $blog['soyad']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo date('d.m.Y H:i', strtotime($blog['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="../blog_detay.php?id=<?php echo $blog['id']; ?>" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    Görüntüle
                                </a>
                                <a href="blog_duzenle.php?id=<?php echo $blog['id']; ?>" 
                                   class="text-orange-600 hover:text-orange-900 mr-3">
                                    Düzenle
                                </a>
                                <a href="?sil=1&id=<?php echo $blog['id']; ?>" 
                                   onclick="return confirm('Bu blog yazısını silmek istediğinizden emin misiniz?')"
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