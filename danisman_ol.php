<?php
require_once 'header.php';

if (isLoggedIn()) {
    redirect('/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad = $_POST['ad'] ?? '';
    $soyad = $_POST['soyad'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $myk_belgesi = $_FILES['myk_belgesi'] ?? null;
    $vergi_levhasi = $_FILES['vergi_levhasi'] ?? null;

    $errors = [];

    // Zorunlu alan kontrolleri
    if (empty($ad)) $errors[] = "Ad alanı zorunludur.";
    if (empty($soyad)) $errors[] = "Soyad alanı zorunludur.";
    if (empty($email)) $errors[] = "E-posta alanı zorunludur.";
    if (empty($telefon)) $errors[] = "Telefon alanı zorunludur.";
    if (empty($password)) $errors[] = "Şifre alanı zorunludur.";
    if ($password !== $password_confirm) $errors[] = "Şifreler eşleşmiyor.";
    if (!$myk_belgesi || $myk_belgesi['error'] !== 0) $errors[] = "MYK belgesi zorunludur.";
    if (!$vergi_levhasi || $vergi_levhasi['error'] !== 0) $errors[] = "Vergi levhası zorunludur.";

    // E-posta kontrolü
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Geçerli bir e-posta adresi giriniz.";
    }

    // E-posta benzersizlik kontrolü
    try {
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "Bu e-posta adresi zaten kullanılıyor.";
        }
    } catch(PDOException $e) {
        $errors[] = "Veritabanı hatası oluştu.";
    }

    // Dosya yükleme kontrolleri
    if (empty($errors)) {
        $myk_belgesi_path = uploadFile($myk_belgesi, 'danisman');
        $vergi_levhasi_path = uploadFile($vergi_levhasi, 'danisman');

        if (!$myk_belgesi_path || !$vergi_levhasi_path) {
            $errors[] = "Dosya yükleme hatası oluştu.";
        }
    }

    // Kayıt işlemi
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                INSERT INTO users (
                    email, password, ad, soyad, telefon, 
                    user_type, status, myk_belgesi, vergi_levhasi
                ) VALUES (
                    ?, ?, ?, ?, ?, 
                    'danisman', 'pending', ?, ?
                )
            ");

            $stmt->execute([
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $ad,
                $soyad,
                $telefon,
                $myk_belgesi_path,
                $vergi_levhasi_path
            ]);

            setFlashMessage('success', 'Başvurunuz alındı. Onay sürecinden sonra size bilgi verilecektir.');
            redirect('login.php');

        } catch(PDOException $e) {
            $errors[] = "Kayıt işlemi sırasında bir hata oluştu.";
        }
    }
}
?>

<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg">
    <h2 class="text-3xl font-bold text-center mb-8">Danışman Başvurusu</h2>

    <?php if (!empty($errors)): ?>
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo generateCSRFToken(); ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Ad -->
            <div>
                <label for="ad" class="block text-sm font-medium text-gray-700">
                    Ad
                </label>
                <input type="text" 
                       id="ad" 
                       name="ad" 
                       required
                       value="<?php echo isset($ad) ? htmlspecialchars($ad) : ''; ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <!-- Soyad -->
            <div>
                <label for="soyad" class="block text-sm font-medium text-gray-700">
                    Soyad
                </label>
                <input type="text" 
                       id="soyad" 
                       name="soyad" 
                       required
                       value="<?php echo isset($soyad) ? htmlspecialchars($soyad) : ''; ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <!-- E-posta -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    E-posta Adresi
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required
                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <!-- Telefon -->
            <div>
                <label for="telefon" class="block text-sm font-medium text-gray-700">
                    Telefon
                </label>
                <input type="tel" 
                       id="telefon" 
                       name="telefon" 
                       required
                       value="<?php echo isset($telefon) ? htmlspecialchars($telefon) : ''; ?>"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <!-- Şifre -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Şifre
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       minlength="6"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <!-- Şifre Tekrar -->
            <div>
                <label for="password_confirm" class="block text-sm font-medium text-gray-700">
                    Şifre Tekrar
                </label>
                <input type="password" 
                       id="password_confirm" 
                       name="password_confirm" 
                       required
                       minlength="6"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>

            <!-- MYK Belgesi -->
            <div>
                <label for="myk_belgesi" class="block text-sm font-medium text-gray-700">
                    MYK Belgesi
                </label>
                <input type="file" 
                       id="myk_belgesi" 
                       name="myk_belgesi" 
                       required
                       accept=".pdf,.jpg,.jpeg,.png"
                       class="mt-1 block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-orange-50 file:text-orange-700
                              hover:file:bg-orange-100">
                <p class="mt-1 text-sm text-gray-500">PDF, JPG veya PNG. Max 5MB.</p>
            </div>

            <!-- Vergi Levhası -->
            <div>
                <label for="vergi_levhasi" class="block text-sm font-medium text-gray-700">
                    Vergi Levhası
                </label>
                <input type="file" 
                       id="vergi_levhasi" 
                       name="vergi_levhasi" 
                       required
                       accept=".pdf,.jpg,.jpeg,.png"
                       class="mt-1 block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-md file:border-0
                              file:text-sm file:font-semibold
                              file:bg-orange-50 file:text-orange-700
                              hover:file:bg-orange-100">
                <p class="mt-1 text-sm text-gray-500">PDF, JPG veya PNG. Max 5MB.</p>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" 
                    class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                Başvuru Yap
            </button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>