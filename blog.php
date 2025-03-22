<?php 
require_once 'header.php';

// Sayfalama
$sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$limit = 9;
$offset = ($sayfa - 1) * $limit;

try {
    // Toplam blog yazısı sayısı
    $stmt = $db->query("SELECT COUNT(*) FROM blog");
    $total = $stmt->fetchColumn();
    $total_pages = ceil($total / $limit);

    // Blog yazılarını getir
    $stmt = $db->prepare("
        SELECT b.*, u.ad, u.soyad 
        FROM blog b 
        JOIN users u ON b.user_id = u.id 
        ORDER BY b.created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    $blog_posts = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Bir hata oluştu.");
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="lg:text-center mb-12">
        <h2 class="text-base text-orange-500 font-semibold tracking-wide uppercase">Blog</h2>
        <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            Son Blog Yazıları
        </p>
        <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
            Arazi değerleme ve gayrimenkul sektörü hakkında güncel bilgiler, haberler ve makaleler.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($blog_posts as $post): ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <?php if ($post['gorsel']): ?>
                    <img src="<?php echo e($post['gorsel']); ?>" 
                         alt="<?php echo e($post['baslik']); ?>" 
                         class="w-full h-48 object-cover">
                <?php else: ?>
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-4xl text-gray-400"></i>
                    </div>
                <?php endif; ?>

                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        <?php echo e($post['baslik']); ?>
                    </h3>

                    <div class="text-sm text-gray-500 mb-4">
                        <span class="flex items-center">
                            <i class="far fa-user mr-2"></i>
                            <?php echo e($post['ad'] . ' ' . $post['soyad']); ?>
                        </span>
                        <span class="flex items-center mt-1">
                            <i class="far fa-calendar mr-2"></i>
                            <?php echo formatDate($post['created_at']); ?>
                        </span>
                    </div>

                    <p class="text-gray-600 mb-4">
                        <?php echo substr(strip_tags($post['icerik']), 0, 150) . '...'; ?>
                    </p>

                    <a href="blog_detay.php?id=<?php echo $post['id']; ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600">
                        Devamını Oku
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="mt-8 flex justify-center">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <?php if ($sayfa > 1): ?>
                    <a href="?sayfa=<?php echo $sayfa - 1; ?>" 
                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Önceki</span>
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?sayfa=<?php echo $i; ?>" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $sayfa ? 'text-orange-500 bg-orange-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($sayfa < $total_pages): ?>
                    <a href="?sayfa=<?php echo $sayfa + 1; ?>" 
                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Sonraki</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>