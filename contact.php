<?php 
require_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_soyad = $_POST['ad_soyad'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $konu = $_POST['konu'] ?? '';
    $mesaj = $_POST['mesaj'] ?? '';

    $errors = [];

    // Validasyon
    if (empty($ad_soyad)) $errors[] = "Ad Soyad alanı zorunludur.";
    if (empty($email)) $errors[] = "E-posta alanı zorunludur.";
    if (empty($telefon)) $errors[] = "Telefon alanı zorunludur.";
    if (empty($konu)) $errors[] = "Konu alanı zorunludur.";
    if (empty($mesaj)) $errors[] = "Mesaj alanı zorunludur.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Geçerli bir e-posta adresi giriniz.";

    if (empty($errors)) {
        try {
            $stmt = $db->prepare("
                INSERT INTO iletisim (ad_soyad, email, telefon, konu, mesaj)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([$ad_soyad, $email, $telefon, $konu, $mesaj]);
            
            setFlashMessage('success', 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.');
            redirect('contact.php');
        } catch(PDOException $e) {
            $errors[] = "Bir hata oluştu. Lütfen daha sonra tekrar deneyin.";
        }
    }
}
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="lg:text-center mb-12">
        <h2 class="text-base text-orange-500 font-semibold tracking-wide uppercase">İletişim</h2>
        <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
            Bizimle İletişime Geçin
        </p>
        <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
            Sorularınız için bize ulaşın, en kısa sürede size dönüş yapalım.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- İletişim Formu -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <?php if (!empty($errors)): ?>
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <?php echo generateCSRFToken(); ?>
                
                <div>
                    <label for="ad_soyad" class="block text-sm font-medium text-gray-700">
                        Ad Soyad
                    </label>
                    <input type="text" 
                           name="ad_soyad" 
                           id="ad_soyad" 
                           required
                           value="<?php echo isset($ad_soyad) ? e($ad_soyad) : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        E-posta Adresi
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           required
                           value="<?php echo isset($email) ? e($email) : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                </div>

                <div>
                    <label for="telefon" class="block text-sm font-medium text-gray-700">
                        Telefon
                    </label>
                    <input type="tel" 
                           name="telefon" 
                           id="telefon" 
                           required
                           value="<?php echo isset($telefon) ? e($telefon) : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                </div>

                <div>
                    <label for="konu" class="block text-sm font-medium text-gray-700">
                        Konu
                    </label>
                    <input type="text" 
                           name="konu" 
                           id="konu" 
                           required
                           value="<?php echo isset($konu) ? e($konu) : ''; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                </div>

                <div>
                    <label for="mesaj" class="block text-sm font-medium text-gray-700">
                        Mesajınız
                    </label>
                    <textarea name="mesaj" 
                              id="mesaj" 
                              rows="4" 
                              required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"><?php echo isset($mesaj) ? e($mesaj) : ''; ?></textarea>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Gönder
                    </button>
                </div>
            </form>
        </div>

        <!-- İletişim Bilgileri -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-6">İletişim Bilgileri</h3>
            
            <div class="space-y-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-map-marker-alt text-orange-500 text-xl"></i>
                    </div>
                    <div class="ml-3 text-base text-gray-500">
                        <p>Merkez Ofis</p>
                        <p>İstanbul, Türkiye</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-phone text-orange-500 text-xl"></i>
                    </div>
                    <div class="ml-3 text-base text-gray-500">
                        <p>Telefon</p>
                        <p><a href="tel:+902121234567" class="hover:text-orange-500">0212 123 45 67</a></p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-envelope text-orange-500 text-xl"></i>
                    </div>
                    <div class="ml-3 text-base text-gray-500">
                        <p>E-posta</p>
                        <p><a href="mailto:info@arazidegerleme.com" class="hover:text-orange-500">info@arazidegerleme.com</a></p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-orange-500 text-xl"></i>
                    </div>
                    <div class="ml-3 text-base text-gray-500">
                        <p>Çalışma Saatleri</p>
                        <p>Pazartesi - Cuma: 09:00 - 18:00</p>
                        <p>Cumartesi: 09:00 - 13:00</p>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Sosyal Medya</h4>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-orange-500">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-orange-500">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-orange-500">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-orange-500">
                        <i class="fab fa-linkedin-in text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>