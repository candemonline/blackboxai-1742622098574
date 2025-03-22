<?php 
require_once 'header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Filtreleme parametreleri
$il = $_GET['il'] ?? '';
$ilce = $_GET['ilce'] ?? '';
$min_fiyat = $_GET['min_fiyat'] ?? '';
$max_fiyat = $_GET['max_fiyat'] ?? '';
$min_metrekare = $_GET['min_metrekare'] ?? '';
$max_metrekare = $_GET['max_metrekare'] ?? '';

// Sayfalama
$sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$limit = 12;
$offset = ($sayfa - 1) * $limit;

try {
    // SQL sorgusu oluştur
    $where = ["status = 'aktif'"];
    $params = [];

    if ($il) {
        $where[] = "il LIKE ?";
        $params[] = "%$il%";
    }
    if ($ilce) {
        $where[] = "ilce LIKE ?";
        $params[] = "%$ilce%";
    }
    if ($min_fiyat) {
        $where[] = "fiyat >= ?";
        $params[] = $min_fiyat;
    }
    if ($max_fiyat) {
        $where[] = "fiyat <= ?";
        $params[] = $max_fiyat;
    }
    if ($min_metrekare) {
        $where[] = "metrekare >= ?";
        $params[] = $min_metrekare;
    }
    if ($max_metrekare) {
        $where[] = "metrekare <= ?";
        $params[] = $max_metrekare;
    }

    $where_clause = implode(" AND ", $where);

    // Toplam ilan sayısı
    $stmt = $db->prepare("SELECT COUNT(*) FROM ilanlar WHERE $where_clause");
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    $total_pages = ceil($total / $limit);

    // İlanları getir
    $stmt = $db->prepare("
        SELECT i.*, u.ad, u.soyad 
        FROM ilanlar i 
        JOIN users u ON i.user_id = u.id 
        WHERE $where_clause 
        ORDER BY i.created_at DESC 
        LIMIT ? OFFSET ?
    ");
    array_push($params, $limit, $offset);
    $stmt->execute($params);
    $ilanlar = $stmt->fetchAll();

    // İlleri getir
    $stmt = $db->query("SELECT DISTINCT il FROM ilanlar WHERE status = 'aktif' ORDER BY il");
    $iller = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // İlçeleri getir (seçili ile göre)
    $ilceler = [];
    if ($il) {
        $stmt = $db->prepare("SELECT DISTINCT ilce FROM ilanlar WHERE il = ? AND status = 'aktif' ORDER BY ilce");
        $stmt->execute([$il]);
        $ilceler = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
} catch(PDOException $e) {
    die("Bir hata oluştu.");
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="lg:text-center mb-12">
        <h2 class="text-base text-orange-500 font-semibold tracking-wide uppercase">İlanlar</h2>
        <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            Arazi İlanları
        </p>
    </div>

    <!-- Filtreler -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label for="il" class="block text-sm font-medium text-gray-700">İl</label>
                <select name="il" id="il" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="">Tümü</option>
                    <?php foreach ($iller as $sehir): ?>
                        <option value="<?php echo e($sehir); ?>" <?php echo $il === $sehir ? 'selected' : ''; ?>>
                            <?php echo e($sehir); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="ilce" class="block text-sm font-medium text-gray-700">İlçe</label>
                <select name="ilce" id="ilce" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" <?php echo empty($ilceler) ? 'disabled' : ''; ?>>
                    <option value="">Tümü</option>
                    <?php foreach ($ilceler as $ilce_adi): ?>
                        <option value="<?php echo e($ilce_adi); ?>" <?php echo $ilce === $ilce_adi ? 'selected' : ''; ?>>
                            <?php echo e($ilce_adi); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="min_fiyat" class="block text-sm font-medium text-gray-700">Min. Fiyat</label>
                <input type="number" name="min_fiyat" id="min_fiyat" value="<?php echo e($min_fiyat); ?>" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div>
                <label for="max_fiyat" class="block text-sm font-medium text-gray-700">Max. Fiyat</label>
                <input type="number" name="max_fiyat" id="max_fiyat" value="<?php echo e($max_fiyat); ?>" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div>
                <label for="min_metrekare" class="block text-sm font-medium text-gray-700">Min. Metrekare</label>
                <input type="number" name="min_metrekare" id="min_metrekare" value="<?php echo e($min_metrekare); ?>" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div>
                <label for="max_metrekare" class="block text-sm font-medium text-gray-700">Max. Metrekare</label>
                <input type="number" name="max_metrekare" id="max_metrekare" value="<?php echo e($max_metrekare); ?>" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <div class="lg:col-span-3 flex justify-end space-x-4">
                <a href="ilanlar.php" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Filtreleri Temizle
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600">
                    Filtrele
                </button>
            </div>
        </form>
    </div>

    <!-- İlanlar -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($ilanlar as $ilan): ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <?php if ($ilan['foto']): ?>
                    <img src="<?php echo e($ilan['foto']); ?>" 
                         alt="<?php echo e($ilan['ilan_basligi']); ?>" 
                         class="w-full h-48 object-cover">
                <?php else: ?>
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-4xl text-gray-400"></i>
                    </div>
                <?php endif; ?>

                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        <?php echo e($ilan['ilan_basligi']); ?>
                    </h3>

                    <div class="space-y-2 text-sm text-gray-500 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt w-5"></i>
                            <?php echo e($ilan['il'] . ' / ' . $ilan['ilce']); ?>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-ruler-combined w-5"></i>
                            <?php echo number_format($ilan['metrekare']); ?> m²
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-user w-5"></i>
                            <?php echo e($ilan['ad'] . ' ' . $ilan['soyad']); ?>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-orange-500">
                            <?php echo number_format($ilan['fiyat'], 2, ',', '.'); ?> ₺
                        </span>
                        <a href="ilan_detay.php?id=<?php echo $ilan['id']; ?>" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600">
                            Detaylar
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($ilanlar)): ?>
        <div class="text-center text-gray-500 py-12">
            <i class="fas fa-search text-4xl mb-4"></i>
            <p>Aramanıza uygun ilan bulunamadı.</p>
        </div>
    <?php endif; ?>

    <!-- Sayfalama -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-8 flex justify-center">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <?php if ($sayfa > 1): ?>
                    <a href="?sayfa=<?php echo $sayfa - 1; ?><?php echo $il ? "&il=$il" : ''; ?><?php echo $ilce ? "&ilce=$ilce" : ''; ?><?php echo $min_fiyat ? "&min_fiyat=$min_fiyat" : ''; ?><?php echo $max_fiyat ? "&max_fiyat=$max_fiyat" : ''; ?><?php echo $min_metrekare ? "&min_metrekare=$min_metrekare" : ''; ?><?php echo $max_metrekare ? "&max_metrekare=$max_metrekare" : ''; ?>" 
                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Önceki</span>
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?sayfa=<?php echo $i; ?><?php echo $il ? "&il=$il" : ''; ?><?php echo $ilce ? "&ilce=$ilce" : ''; ?><?php echo $min_fiyat ? "&min_fiyat=$min_fiyat" : ''; ?><?php echo $max_fiyat ? "&max_fiyat=$max_fiyat" : ''; ?><?php echo $min_metrekare ? "&min_metrekare=$min_metrekare" : ''; ?><?php echo $max_metrekare ? "&max_metrekare=$max_metrekare" : ''; ?>" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i === $sayfa ? 'text-orange-500 bg-orange-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($sayfa < $total_pages): ?>
                    <a href="?sayfa=<?php echo $sayfa + 1; ?><?php echo $il ? "&il=$il" : ''; ?><?php echo $ilce ? "&ilce=$ilce" : ''; ?><?php echo $min_fiyat ? "&min_fiyat=$min_fiyat" : ''; ?><?php echo $max_fiyat ? "&max_fiyat=$max_fiyat" : ''; ?><?php echo $min_metrekare ? "&min_metrekare=$min_metrekare" : ''; ?><?php echo $max_metrekare ? "&max_metrekare=$max_metrekare" : ''; ?>" 
                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Sonraki</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('il').addEventListener('change', function() {
    const ilSelect = this;
    const ilceSelect = document.getElementById('ilce');
    
    if (!ilSelect.value) {
        ilceSelect.innerHTML = '<option value="">Tümü</option>';
        ilceSelect.disabled = true;
        return;
    }

    fetch(`get_ilceler.php?il=${encodeURIComponent(ilSelect.value)}`)
        .then(response => response.json())
        .then(ilceler => {
            ilceSelect.innerHTML = '<option value="">Tümü</option>';
            ilceler.forEach(ilce => {
                const option = document.createElement('option');
                option.value = ilce;
                option.textContent = ilce;
                ilceSelect.appendChild(option);
            });
            ilceSelect.disabled = false;
        })
        .catch(error => {
            console.error('İlçeler yüklenirken bir hata oluştu:', error);
            ilceSelect.innerHTML = '<option value="">Tümü</option>';
            ilceSelect.disabled = true;
        });
});
</script>

<?php require_once 'footer.php'; ?>