<?php 
include 'header.php';

// Blog ID kontrolü
$blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$blog_id) {
    redirect('blog.php');
}

// Blog yazısını getir
try {
    $stmt = $db->prepare("SELECT * FROM blog WHERE id = ?");
    $stmt->execute([$blog_id]);
    $blog = $stmt->fetch();
    
    if (!$blog) {
        redirect('blog.php');
    }
} catch(PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

// Form gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $baslik = $_POST['baslik'] ?? '';
    $icerik = $_POST['icerik'] ?? '';
    $gorsel = $_FILES['gorsel'] ?? null;
    $errors = [];

    // Validasyon
    if (empty($baslik)) {
        $errors[] = "Başlık alanı zorunludur.";
    }
    if (empty($icerik)) {
        $errors[] = "İçerik alanı zorunludur.";
    }

    // Görsel yükleme
    $gorsel_path = $blog['gorsel']; // Mevcut görsel yolu
    if ($gorsel && $gorsel['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($gorsel['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            $errors[] = "Sadece JPG, JPEG, PNG ve WEBP formatları desteklenir.";
        } elseif ($gorsel['size'] > 5242880) { // 5MB
            $errors[] = "Görsel boyutu 5MB'dan küçük olmalıdır.";
        } else {
            // Eski görseli sil
            if ($blog['gorsel'] && file_exists('../' . $blog['gorsel'])) {
                unlink('../' . $blog['gorsel']);
            }
            
            // Yeni görseli yükle
            $gorsel_path = 'uploads/blog/' . uniqid() . '.' . $ext;
            if (!move_uploaded_file($gorsel['tmp_name'], '../' . $gorsel_path)) {
                $errors[] = "Görsel yüklenirken bir hata oluştu.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                UPDATE blog 
                SET baslik = ?, icerik = ?, gorsel = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            
            $stmt->execute([
                $baslik,
                $icerik,
                $gorsel_path,
                $blog_id
            ]);

            setFlashMessage('success', 'Blog yazısı başarıyla güncellendi.');
            redirect('blog.php');
            
        } catch(PDOException $e) {
            // Hata durumunda yeni yüklenen görseli sil
            if ($gorsel_path != $blog['gorsel'] && file_exists('../' . $gorsel_path)) {
                unlink('../' . $gorsel_path);
            }
            $errors[] = "Veritabanı hatası: " . $e->getMessage();
        }
    }
}
?>

<div class="mb-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Blog Yazısı Düzenle</h2>
        <a href="blog.php" class="text-orange-500 hover:text-orange-600">
            <i class="fas fa-arrow-left mr-2"></i>
            Geri Dön
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg p-6">
        <div class="mb-4">
            <label for="baslik" class="block text-sm font-medium text-gray-700 mb-2">
                Başlık
            </label>
            <input type="text" 
                   id="baslik" 
                   name="baslik" 
                   required
                   value="<?php echo htmlspecialchars($blog['baslik']); ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500">
        </div>

        <div class="mb-4">
            <label for="icerik" class="block text-sm font-medium text-gray-700 mb-2">
                İçerik
            </label>
            <textarea id="icerik" 
                      name="icerik" 
                      required
                      rows="10"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-orange-500"><?php echo htmlspecialchars($blog['icerik']); ?></textarea>
        </div>

        <div class="mb-4">
            <label for="gorsel" class="block text-sm font-medium text-gray-700 mb-2">
                Görsel
            </label>
            <?php if ($blog['gorsel']): ?>
                <div class="mb-2">
                    <img src="<?php echo '../' . htmlspecialchars($blog['gorsel']); ?>" 
                         alt="Mevcut görsel"
                         class="max-w-xs">
                </div>
            <?php endif; ?>
            <input type="file" 
                   id="gorsel" 
                   name="gorsel" 
                   accept=".jpg,.jpeg,.png,.webp"
                   onchange="previewImage(this, 'gorselPreview')"
                   class="w-full">
            <p class="mt-1 text-sm text-gray-500">
                Maksimum boyut: 5MB. İzin verilen formatlar: JPG, JPEG, PNG, WEBP
            </p>
            <img id="gorselPreview" 
                 src="#" 
                 alt="Yeni görsel önizleme" 
                 class="mt-2 max-w-xs hidden">
        </div>

        <div class="flex justify-end">
            <button type="submit" 
                    class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600">
                <i class="fas fa-save mr-2"></i>
                Kaydet
            </button>
        </div>
    </form>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'footer.php'; ?>