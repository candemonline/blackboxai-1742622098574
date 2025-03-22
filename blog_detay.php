<?php 
require_once 'header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $stmt = $db->prepare("
        SELECT b.*, u.ad, u.soyad 
        FROM blog b 
        JOIN users u ON b.user_id = u.id 
        WHERE b.id = ?
    ");
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    if (!$post) {
        redirect('/blog.php');
    }

    // Diğer blog yazılarını getir
    $stmt = $db->prepare("
        SELECT id, baslik, gorsel 
        FROM blog 
        WHERE id != ? 
        ORDER BY created_at DESC 
        LIMIT 3
    ");
    $stmt->execute([$id]);
    $other_posts = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Bir hata oluştu.");
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Ana İçerik -->
        <div class="lg:col-span-2">
            <article class="bg-white rounded-lg shadow-lg overflow-hidden">
                <?php if ($post['gorsel']): ?>
                    <img src="<?php echo e($post['gorsel']); ?>" 
                         alt="<?php echo e($post['baslik']); ?>" 
                         class="w-full h-96 object-cover">
                <?php endif; ?>

                <div class="p-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">
                        <?php echo e($post['baslik']); ?>
                    </h1>

                    <div class="flex items-center text-sm text-gray-500 mb-6">
                        <div class="flex items-center">
                            <i class="far fa-user mr-2"></i>
                            <?php echo e($post['ad'] . ' ' . $post['soyad']); ?>
                        </div>
                        <span class="mx-2">•</span>
                        <div class="flex items-center">
                            <i class="far fa-calendar mr-2"></i>
                            <?php echo formatDateTime($post['created_at']); ?>
                        </div>
                    </div>

                    <div class="prose prose-lg max-w-none">
                        <?php echo $post['icerik']; ?>
                    </div>

                    <!-- Sosyal Medya Paylaşım -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bu yazıyı paylaş</h3>
                        <div class="flex space-x-4">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/blog_detay.php?id=' . $post['id']); ?>" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-700">
                                <i class="fab fa-facebook-f text-xl"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/blog_detay.php?id=' . $post['id']); ?>&text=<?php echo urlencode($post['baslik']); ?>" 
                               target="_blank"
                               class="text-blue-400 hover:text-blue-500">
                                <i class="fab fa-twitter text-xl"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(SITE_URL . '/blog_detay.php?id=' . $post['id']); ?>&title=<?php echo urlencode($post['baslik']); ?>" 
                               target="_blank"
                               class="text-blue-700 hover:text-blue-800">
                                <i class="fab fa-linkedin-in text-xl"></i>
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($post['baslik'] . ' ' . SITE_URL . '/blog_detay.php?id=' . $post['id']); ?>" 
                               target="_blank"
                               class="text-green-600 hover:text-green-700">
                                <i class="fab fa-whatsapp text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <!-- Yan Panel -->
        <div class="lg:col-span-1">
            <!-- Diğer Blog Yazıları -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Diğer Yazılar</h3>
                <div class="space-y-4">
                    <?php foreach ($other_posts as $other_post): ?>
                        <a href="blog_detay.php?id=<?php echo $other_post['id']; ?>" 
                           class="flex items-center space-x-4 group">
                            <?php if ($other_post['gorsel']): ?>
                                <img src="<?php echo e($other_post['gorsel']); ?>" 
                                     alt="<?php echo e($other_post['baslik']); ?>" 
                                     class="w-20 h-20 object-cover rounded">
                            <?php else: ?>
                                <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-image text-2xl text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <h4 class="text-gray-900 group-hover:text-orange-500 transition-colors duration-200">
                                    <?php echo e($other_post['baslik']); ?>
                                </h4>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- İletişim Kartı -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bizimle İletişime Geçin</h3>
                <p class="text-gray-600 mb-4">
                    Arazi değerleme ve danışmanlık hizmetlerimiz hakkında bilgi almak için bize ulaşın.
                </p>
                <a href="/contact.php" 
                   class="inline-flex items-center justify-center w-full px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600">
                    İletişime Geç
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>