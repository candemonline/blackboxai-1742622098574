<?php 
include 'header.php';

// Durum filtresi
$durum = isset($_GET['durum']) ? $_GET['durum'] : 'bekleyen';
$durumlar = ['bekleyen' => 'beklemede', 'teklif' => 'teklif_verildi', 'iptal' => 'iptal'];
$current_status = $durumlar[$durum] ?? 'beklemede';

// Teklif verme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['teklif_ver'])) {
    $talep_id = (int)$_POST['talep_id'];
    $teklif_miktar = (float)$_POST['teklif_miktar'];
    
    try {
        $stmt = $db->prepare("UPDATE arazi_talepleri SET status = 'teklif_verildi', teklif_miktar = ? WHERE id = ?");
        $stmt->execute([$teklif_miktar, $talep_id]);
        $success = "Teklif başarıyla verildi.";
    } catch(PDOException $e) {
        $error = "Teklif verme işlemi başarısız oldu.";
    }
}

// İptal işlemi
if (isset($_GET['iptal']) && isset($_GET['id'])) {
    $talep_id = (int)$_GET['id'];
    
    try {
        $stmt = $db->prepare("UPDATE arazi_talepleri SET status = 'iptal' WHERE id = ?");
        $stmt->execute([$talep_id]);
        $success = "Talep başarıyla iptal edildi.";
    } catch(PDOException $e) {
        $error = "İptal işlemi başarısız oldu.";
    }
}

try {
    // Talepleri getir
    $stmt = $db->prepare("
        SELECT * FROM arazi_talepleri 
        WHERE status = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$current_status]);
    $talepler = $stmt->fetchAll();
    
} catch(PDOException $e) {
    $error = "Veritabanı hatası: " . $e->getMessage();
}
?>

<div class="mb-6">
    <!-- Durum Sekmeleri -->
    <div class="flex border-b mb-6">
        <a href="?durum=bekleyen" 
           class="px-6 py-3 <?php echo $durum == 'bekleyen' ? 'border-b-2 border-orange-500 text-orange-500' : 'text-gray-500'; ?>">
            Bekleyen Talepler
        </a>
        <a href="?durum=teklif" 
           class="px-6 py-3 <?php echo $durum == 'teklif' ? 'border-b-2 border-orange-500 text-orange-500' : 'text-gray-500'; ?>">
            Teklif Verilenler
        </a>
        <a href="?durum=iptal" 
           class="px-6 py-3 <?php echo $durum == 'iptal' ? 'border-b-2 border-orange-500 text-orange-500' : 'text-gray-500'; ?>">
            İptal Edilenler
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

    <!-- Talepler Tablosu -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <?php if (empty($talepler)): ?>
            <div class="p-6 text-center text-gray-500">
                Bu durumda talep bulunmuyor.
            </div>
        <?php else: ?>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Talep No
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İl/İlçe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mahalle
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ada/Parsel
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tarih
                        </th>
                        <?php if ($durum == 'teklif'): ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Teklif
                            </th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            İşlemler
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($talepler as $talep): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                #<?php echo $talep['id']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($talep['il'] . '/' . $talep['ilce']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($talep['mahalle']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($talep['ada'] . '/' . $talep['parsel']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo date('d.m.Y', strtotime($talep['created_at'])); ?>
                            </td>
                            <?php if ($durum == 'teklif'): ?>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo number_format($talep['teklif_miktar'], 2, ',', '.'); ?> ₺
                                </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <?php if ($durum == 'bekleyen'): ?>
                                    <button onclick="showTeklifModal(<?php echo $talep['id']; ?>)" 
                                            class="text-green-600 hover:text-green-900 mr-3">
                                        Teklif Ver
                                    </button>
                                    <a href="?durum=bekleyen&iptal=1&id=<?php echo $talep['id']; ?>" 
                                       onclick="return confirm('Bu talebi iptal etmek istediğinizden emin misiniz?')"
                                       class="text-red-600 hover:text-red-900">
                                        İptal Et
                                    </a>
                                <?php else: ?>
                                    <button onclick="showTalepDetay(<?php echo $talep['id']; ?>)" 
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

<!-- Teklif Verme Modal -->
<div id="teklifModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Teklif Ver</h3>
            
            <form method="POST" onsubmit="return validateTeklifForm()">
                <input type="hidden" name="talep_id" id="teklifTalepId">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="teklif_miktar">
                        Teklif Miktarı (₺)
                    </label>
                    <input type="number" 
                           id="teklif_miktar" 
                           name="teklif_miktar" 
                           step="0.01" 
                           required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="hideTeklifModal()"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        İptal
                    </button>
                    <button type="submit" 
                            name="teklif_ver"
                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Teklif Ver
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showTeklifModal(talepId) {
    document.getElementById('teklifTalepId').value = talepId;
    document.getElementById('teklifModal').classList.remove('hidden');
}

function hideTeklifModal() {
    document.getElementById('teklifModal').classList.add('hidden');
}

function validateTeklifForm() {
    const miktar = document.getElementById('teklif_miktar').value;
    if (!miktar || miktar <= 0) {
        alert('Lütfen geçerli bir teklif miktarı giriniz.');
        return false;
    }
    return true;
}

// Modal dışına tıklandığında kapatma
document.getElementById('teklifModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideTeklifModal();
    }
});
</script>

<?php include 'footer.php'; ?>