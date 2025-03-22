<?php 
include 'header.php';

// İlan ID kontrolü
$ilan_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$ilan_id) {
    header('Location: ilanlar.php');
    exit;
}

// İlanı getir
try {
    $stmt = $db->prepare("SELECT * FROM ilanlar WHERE id = ?");
    $stmt->execute([$ilan_id]);
    $ilan = $stmt->fetch();
    
    if (!$ilan) {
        header('Location: ilanlar.php');
        exit;
    }
} catch(PDOException $e) {
    $error = "İlan bulunamadı.";
}

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ilan_basligi = $_POST['ilan_basligi'] ?? '';
    $metrekare = (int)$_POST['metrekare'] ?? 0;
    $fiyat = (float)$_POST['fiyat'] ?? 0;
    $ada = $_POST['ada'] ?? '';
    $parsel = $_POST['parsel'] ?? '';
    $tkgm_link = $_POST['tkgm_link'] ?? '';
    $il = $_POST['il'] ?? '';
    $ilce = $_POST['ilce'] ?? '';
    $mahalle = $_POST['mahalle'] ?? '';
    $foto = $_FILES['foto'] ?? null;
    
    $errors = [];
    
    // Zorunlu alan kontrolleri
    if (empty($ilan_basligi)) $errors[] = "İlan başlığı zorunludur.";
    if ($metrekare <= 0) $errors[] = "Geçerli bir metrekare giriniz.";
    if ($fiyat <= 0) $errors[] = "Geçerli bir fiyat giriniz.";
    if (empty($ada)) $errors[] = "Ada numarası zorunludur.";
    if (empty($parsel)) $errors[] = "Parsel numarası zorunludur.";
    if (empty($il)) $errors[] = "İl seçimi zorunludur.";
    if (empty($ilce)) $errors[] = "İlçe seçimi zorunludur.";
    if (empty($mahalle)) $errors[] = "Mahalle zorunludur.";
    
    // Fotoğraf kontrolü
    $foto_path = $ilan['foto']; // Mevcut fotoğraf yolu
    if ($foto && $foto['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($foto['type'], $allowed_types)) {
            $errors[] = "Sadece JPEG, PNG ve WEBP formatında görseller yüklenebilir.";
        }
        
        if ($foto['size'] > $max_size) {
            $errors[] = "Görsel boyutu en fazla 5MB olabilir.";
        }
        
        if (empty($errors)) {
            $upload_dir = '../uploads/ilanlar/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Eski fotoğrafı sil
            if ($ilan['foto'] && file_exists('../' . $ilan['foto'])) {
                unlink('../' . $ilan['foto']);
            }
            
            $filename = uniqid() . '_' . basename($foto['name']);
            $foto_path = 'uploads/ilanlar/' . $filename;
            
            if (!move_uploaded_file($foto['tmp_name'], $upload_dir . $filename)) {
                $errors[] = "Görsel yüklenirken bir hata oluştu.";
            }
        }
    }
    
    // Hata yoksa güncelle
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                UPDATE ilanlar SET 
                    ilan_basligi = ?, 
                    metrekare = ?, 
                    fiyat = ?, 
                    ada = ?, 
                    parsel = ?, 
                    tkgm_link = ?, 
                    il = ?, 
                    ilce = ?, 
                    mahalle = ?, 
                    foto = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([
                $ilan_basligi,
                $metrekare,
                $fiyat,
                $ada,
                $parsel,
                $tkgm_link,
                $il,
                $ilce,
                $mahalle,
                $foto_path,
                $ilan_id
            ]);
            
            header('Location: ilanlar.php');
            exit;
            
        } catch(PDOException $e) {
            $errors[] = "Veritabanı hatası: " . $e->getMessage();
        }
    }
}
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">İlan Düzenle</h2>
        <a href="ilanlar.php" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i> Geri Dön
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- İlan Başlığı -->
            <div class="col-span-2">
                <label for="ilan_basligi" class="block text-gray-700 text-sm font-bold mb-2">
                    İlan Başlığı
                </label>
                <input type="text" 
                       id="ilan_basligi" 
                       name="ilan_basligi" 
                       required
                       value="<?php echo htmlspecialchars($ilan['ilan_basligi']); ?>"
                       class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- Metrekare -->
            <div>
                <label for="metrekare" class="block text-gray-700 text-sm font-bold mb-2">
                    Metrekare
                </label>
                <input type="number" 
                       id="metrekare" 
                       name="metrekare" 
                       required
                       min="1"
                       value="<?php echo $ilan['metrekare']; ?>"
                       class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- Fiyat -->
            <div>
                <label for="fiyat" class="block text-gray-700 text-sm font-bold mb-2">
                    Fiyat (₺)
                </label>
                <input type="number" 
                       id="fiyat" 
                       name="fiyat" 
                       required
                       min="1"
                       step="0.01"
                       value="<?php echo $ilan['fiyat']; ?>"
                       class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- Ada/Parsel -->
            <div>
                <label for="ada" class="block text-gray-700 text-sm font-bold mb-2">
                    Ada No
                </label>
                <input type="text" 
                       id="ada" 
                       name="ada" 
                       required
                       value="<?php echo htmlspecialchars($ilan['ada']); ?>"
                       class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div>
                <label for="parsel" class="block text-gray-700 text-sm font-bold mb-2">
                    Parsel No
                </label>
                <input type="text" 
                       id="parsel" 
                       name="parsel" 
                       required
                       value="<?php echo htmlspecialchars($ilan['parsel']); ?>"
                       class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- TKGM Link -->
            <div class="col-span-2">
                <label for="tkgm_link" class="block text-gray-700 text-sm font-bold mb-2">
                    TKGM Parsel Sorgu Linki
                </label>
                <input type="url" 
                       id="tkgm_link" 
                       name="tkgm_link"
                       value="<?php echo htmlspecialchars($ilan['tkgm_link']); ?>"
                       class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- İl/İlçe/Mahalle -->
            <div>
                <label for="il" class="block text-gray-700 text-sm font-bold mb-2">
                    İl
                </label>
                <select id="il" 
                        name="il" 
                        required
                        class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">İl Seçiniz</option>
                    <option value="istanbul" <?php echo $ilan['il'] == 'istanbul' ? 'selected' : ''; ?>>İstanbul</option>
                    <option value="ankara" <?php echo $ilan['il'] == 'ankara' ? 'selected' : ''; ?>>Ankara</option>
                    <option value="izmir" <?php echo $ilan['il'] == 'izmir' ? 'selected' : ''; ?>>İzmir</option>
                </select>
            </div>

            <div>
                <label for="ilce" class="block text-gray-700 text-sm font-bold mb-2">
                    İlçe
                </label>
                <select id="ilce" 
                        name="ilce" 
                        required
                        class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">İlçe Seçiniz</option>
                </select>
            </div>

            <div class="col-span-2">
                <label for="mahalle" class="block text-gray-700 text-sm font-bold mb-2">
                    Mahalle
                </label>
                <input type="text" 
                       id="mahalle" 
                       name="mahalle" 
                       required
                       value="<?php echo htmlspecialchars($ilan['mahalle']); ?>"
                       class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- Fotoğraf -->
            <div class="col-span-2">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Fotoğraf
                </label>
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <img id="fotoOnizleme" 
                             src="<?php echo $ilan['foto'] ? '../' . htmlspecialchars($ilan['foto']) : '#'; ?>" 
                             alt="Fotoğraf önizleme" 
                             class="w-32 h-32 object-cover rounded border <?php echo $ilan['foto'] ? '' : 'hidden'; ?>">
                    </div>
                    <div class="flex-grow">
                        <input type="file" 
                               id="foto" 
                               name="foto" 
                               accept="image/jpeg,image/png,image/webp"
                               class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               onchange="previewImage(this, 'fotoOnizleme')">
                        <p class="mt-1 text-sm text-gray-500">
                            Maksimum dosya boyutu: 5MB. İzin verilen formatlar: JPEG, PNG, WEBP
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-6">
            <button type="submit" 
                    class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-save mr-2"></i> Güncelle
            </button>
        </div>
    </form>
</div>

<script>
// İl-İlçe Seçimi
const ilceler = {
    istanbul: ['Kadıköy', 'Beşiktaş', 'Şişli', 'Üsküdar'],
    ankara: ['Çankaya', 'Keçiören', 'Mamak', 'Yenimahalle'],
    izmir: ['Konak', 'Karşıyaka', 'Bornova', 'Buca']
};

document.getElementById('il').addEventListener('change', function() {
    const ilceSelect = document.getElementById('ilce');
    const selectedIl = this.value;
    
    // İlçeleri temizle
    ilceSelect.innerHTML = '<option value="">İlçe Seçiniz</option>';
    
    // Seçilen ile göre ilçeleri ekle
    if (selectedIl && ilceler[selectedIl]) {
        ilceler[selectedIl].forEach(ilce => {
            const option = document.createElement('option');
            option.value = ilce.toLowerCase();
            option.textContent = ilce;
            if (ilce.toLowerCase() === '<?php echo strtolower($ilan['ilce']); ?>') {
                option.selected = true;
            }
            ilceSelect.appendChild(option);
        });
    }
});

// Sayfa yüklendiğinde ilçeleri yükle
window.addEventListener('load', function() {
    const ilSelect = document.getElementById('il');
    if (ilSelect.value) {
        ilSelect.dispatchEvent(new Event('change'));
    }
});

// Görsel önizleme
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        preview.src = '#';
        preview.classList.add('hidden');
    }
}
</script>

<?php include 'footer.php'; ?>