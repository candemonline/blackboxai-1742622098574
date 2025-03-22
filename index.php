<?php require_once 'header.php'; ?>

<div class="relative bg-orange-500">
    <div class="absolute inset-0">
        <img class="w-full h-full object-cover" src="https://images.pexels.com/photos/280229/pexels-photo-280229.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="Arazi">
        <div class="absolute inset-0 bg-orange-500 mix-blend-multiply"></div>
    </div>
    <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">Arazi Değerleme</h1>
        <p class="mt-6 text-xl text-orange-100 max-w-3xl">
            Profesyonel arazi değerleme ve danışmanlık hizmetleri ile size en doğru değeri sunuyoruz.
        </p>
        <div class="mt-10">
            <a href="/danisman_ol.php" class="inline-block bg-white py-3 px-8 rounded-lg text-orange-500 font-semibold hover:bg-orange-50">
                Danışman Olun
            </a>
        </div>
    </div>
</div>

<div class="py-12 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center">
            <h2 class="text-base text-orange-500 font-semibold tracking-wide uppercase">Hizmetlerimiz</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Size Nasıl Yardımcı Olabiliriz?
            </p>
        </div>

        <div class="mt-10">
            <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-3">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-chart-line text-3xl text-orange-500"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Arazi Değerleme</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    Arazinizin güncel piyasa değerini profesyonel yöntemlerle belirleriz.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-handshake text-3xl text-orange-500"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Danışmanlık</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    Arazi alım-satım süreçlerinde profesyonel danışmanlık hizmeti sunarız.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-contract text-3xl text-orange-500"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Resmi İşlemler</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    Tapu ve kadastro işlemlerinde size yardımcı oluruz.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center mb-12">
            <h2 class="text-base text-orange-500 font-semibold tracking-wide uppercase">Son İlanlar</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Güncel Arazi İlanları
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            try {
                $stmt = $db->query("
                    SELECT i.*, u.ad, u.soyad 
                    FROM ilanlar i 
                    JOIN users u ON i.user_id = u.id 
                    WHERE i.status = 'aktif' 
                    ORDER BY i.created_at DESC 
                    LIMIT 6
                ");
                
                while ($ilan = $stmt->fetch()) {
                    ?>
                    <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                        <?php if ($ilan['foto']): ?>
                            <img class="w-full h-48 object-cover" src="<?php echo $ilan['foto']; ?>" alt="<?php echo e($ilan['ilan_basligi']); ?>">
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                <?php echo e($ilan['ilan_basligi']); ?>
                            </h3>
                            <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                                <span><?php echo e($ilan['il']); ?> / <?php echo e($ilan['ilce']); ?></span>
                                <span><?php echo number_format($ilan['metrekare']); ?> m²</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold text-orange-500">
                                    <?php echo number_format($ilan['fiyat'], 2, ',', '.'); ?> ₺
                                </span>
                                <a href="ilan_detay.php?id=<?php echo $ilan['id']; ?>" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-orange-500 bg-orange-100 hover:bg-orange-200">
                                    Detaylar
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } catch(PDOException $e) {
                echo '<div class="text-center text-red-500">İlanlar yüklenirken bir hata oluştu.</div>';
            }
            ?>
        </div>

        <div class="text-center mt-10">
            <a href="/ilanlar.php" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600">
                Tüm İlanları Gör
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</div>

<div class="bg-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:text-center mb-12">
            <h2 class="text-base text-orange-500 font-semibold tracking-wide uppercase">Blog</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Son Blog Yazıları
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            try {
                $stmt = $db->query("
                    SELECT b.*, u.ad, u.soyad 
                    FROM blog b 
                    JOIN users u ON b.user_id = u.id 
                    ORDER BY b.created_at DESC 
                    LIMIT 3
                ");
                
                while ($blog = $stmt->fetch()) {
                    ?>
                    <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                        <?php if ($blog['gorsel']): ?>
                            <img class="w-full h-48 object-cover" src="<?php echo $blog['gorsel']; ?>" alt="<?php echo e($blog['baslik']); ?>">
                        <?php endif; ?>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                <?php echo e($blog['baslik']); ?>
                            </h3>
                            <p class="text-gray-500 mb-4">
                                <?php echo substr(strip_tags($blog['icerik']), 0, 150) . '...'; ?>
                            </p>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">
                                    <?php echo e($blog['ad'] . ' ' . $blog['soyad']); ?>
                                </span>
                                <a href="blog_detay.php?id=<?php echo $blog['id']; ?>" 
                                   class="text-orange-500 hover:text-orange-600">
                                    Devamını Oku
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } catch(PDOException $e) {
                echo '<div class="text-center text-red-500">Blog yazıları yüklenirken bir hata oluştu.</div>';
            }
            ?>
        </div>

        <div class="text-center mt-10">
            <a href="/blog.php" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600">
                Tüm Yazıları Gör
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>