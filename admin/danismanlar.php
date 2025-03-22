<?php 
include 'header.php';

// Durum filtresi
$durum = isset($_GET['durum']) ? $_GET['durum'] : 'onay_bekleyen';
$durumlar = ['onay_bekleyen' => 'pending', 'aktif' => 'active', 'reddedilen' => 'rejected'];
$current_status = $durumlar[$durum] ?? 'pending';

// Onaylama işlemi
if (isset($_GET['onay']) && isset($_GET['id'])) {
    $danisman_id = (int)$_GET['id'];
    
    try {
        $stmt = $db->prepare("UPDATE users SET status = 'active' WHERE id = ? AND user_type = 'danisman'");
        $stmt->execute([$danisman_id]);
        $success = "Danışman başvurusu başarıyla onaylandı.";
    } catch(PDOException $e) {
        $error = "Onaylama işlemi başarısız oldu.";
    }
}

// Reddetme işlemi
if (isset($_GET['red']) && isset($_GET['id'])) {
    $danisman_id = (int)$_GET['id'];
    
    try {
        $stmt = $db->prepare("UPDATE users SET status = 'rejected' WHERE id = ? AND user_type = 'danisman'");
        $stmt->execute([$danisman_id]);
        $success = "Danışman başvurusu reddedildi.";
    } catch(PDOException $e) {
        $error = "Reddetme işlemi başarısız oldu.";
    }
}

try {
    // Danışmanları getir
    $stmt = $db->prepare("
        SELECT * FROM users 
        WHERE user_type = 'danisman' AND status = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$current_status]);
    $danismanlar = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Veritabanı hatası: " . $e->getMessage();
}
?>

<div class="mb-6">
    <!-- Durum Sekmeleri -->
    <div class="flex border-b mb-6">
        <a href="?durum=onay_bekleyen" 
           class="px-6 py-3 <?php echo $durum == 'onay_bekleyen' ? 'border-b-2 border-orange-500 text-orange-500' : 'text-gray-500'; ?>">
            Onay Bekleyenler
        </a>
        <a href="?durum=aktif" 
           class="px-6 py-3 <?php echo $durum == 'aktif' ? 'border-b-2 border-orange-500 text-orange-500' : 'text-gray-500'; ?>">
            Aktif Danışmanlar
        </a>
        <a href="?durum=reddedilen" 
           class="px-6 py-3 <?php echo $durum == 'reddedilen' ? 'border-b-2 border-orange-500 text-orange-500' : 'text-gray-500'; ?>">
            Reddedilenler
        </a>
    </div>

    <?php if (isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <!-- Danışmanlar Tablosu -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <?php if (empty($danismanlar)): ?>
            <div class="p-6 text-center text-gray-500">
                Bu durumda danışman bulunmuyor.
            </div>
        <?php else: ?>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ad Soyad
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            E-posta
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Telefon
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Başvuru Tarihi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Belgeler
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($danismanlar as $danisman): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($danisman['ad'] . ' ' . $danisman['soyad']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($danisman['email']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($danisman['telefon']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo date('d.m.Y', strtotime($danisman['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($danisman['myk_belgesi']): ?>
                                    <a href="<?php echo htmlspecialchars($danisman['myk_belgesi']); ?>" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-900 mr-3">
                                        MYK Belgesi
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($danisman['vergi_levhasi']): ?>
                                    <a href="<?php echo htmlspecialchars($danisman['vergi_levhasi']); ?>" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-900">
                                        Vergi Levhası
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <?php if ($durum == 'onay_bekleyen'): ?>
                                    <a href="?durum=onay_bekleyen&onay=1&id=<?php echo $danisman['id']; ?>" 
                                       onclick="return confirm('Bu danışmanı onaylamak istediğinizden emin misiniz?')"
                                       class="text-green-600 hover:text-green-900 mr-3">
                                        Onayla
                                    </a>
                                    <a href="?durum=onay_bekleyen&red=1&id=<?php echo $danisman['id']; ?>" 
                                       onclick="return confirm('Bu danışmanı reddetmek istediğinizden emin misiniz?')"
                                       class="text-red-600 hover:text-red-900">
                                        Reddet
                                    </a>
                                <?php else: ?>
                                    <button onclick="showDanismanDetay(<?php echo $danisman['id']; ?>)" 
                                            class="text-blue-600 hover:text-blue-900">
                                        Detay
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Danışman Detay Modal -->
<div id="danismanDetayModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Danışman Detayları</h3>
                <button onclick="hideDanismanDetayModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="danismanDetayContent" class="space-y-4">
                <!-- JavaScript ile doldurulacak -->
            </div>
        </div>
    </div>
</div>

<script>
function showDanismanDetay(danismanId) {
    // AJAX ile danışman detaylarını getir
    fetch(`get_danisman_detay.php?id=${danismanId}`)
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('danismanDetayContent');
            content.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold">Ad Soyad</label>
                        <p>${data.ad} ${data.soyad}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">E-posta</label>
                        <p>${data.email}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">Telefon</label>
                        <p>${data.telefon}</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">Kayıt Tarihi</label>
                        <p>${data.created_at}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-gray-700 font-semibold">Belgeler</label>
                    <div class="flex space-x-4 mt-2">
                        ${data.myk_belgesi ? `
                            <a href="${data.myk_belgesi}" target="_blank" 
                               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                MYK Belgesi
                            </a>
                        ` : ''}
                        ${data.vergi_levhasi ? `
                            <a href="${data.vergi_levhasi}" target="_blank"
                               class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Vergi Levhası
                            </a>
                        ` : ''}
                    </div>
                </div>
            `;
            document.getElementById('danismanDetayModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Hata:', error);
            alert('Danışman detayları alınırken bir hata oluştu.');
        });
}

function hideDanismanDetayModal() {
    document.getElementById('danismanDetayModal').classList.add('hidden');
}

// Modal dışına tıklandığında kapatma
document.getElementById('danismanDetayModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDanismanDetayModal();
    }
});
</script>

<?php include 'footer.php'; ?>