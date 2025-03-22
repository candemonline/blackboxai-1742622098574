<?php
require_once 'header.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('/admin');
    } else {
        redirect('/');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken()) {
        setFlashMessage('error', 'Güvenlik doğrulaması başarısız oldu. Lütfen sayfayı yenileyip tekrar deneyin.');
        redirect('/login.php');
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    try {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'pending') {
                setFlashMessage('error', 'Hesabınız henüz onaylanmamış.');
            } elseif ($user['status'] === 'rejected') {
                setFlashMessage('error', 'Hesabınız reddedildi.');
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['user_name'] = $user['ad'] . ' ' . $user['soyad'];

                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (86400 * 30), '/');
                    
                    $stmt = $db->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                    $stmt->execute([$token, $user['id']]);
                }

                if ($user['user_type'] === 'admin') {
                    redirect('/admin');
                } else {
                    redirect('/');
                }
            }
        } else {
            setFlashMessage('error', 'Geçersiz e-posta veya şifre.');
        }
    } catch(PDOException $e) {
        setFlashMessage('error', 'Bir hata oluştu. Lütfen tekrar deneyin.');
    }
}

$flash = getFlashMessage();
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                Giriş Yap
            </h2>
            <p class="text-sm text-gray-600">
                Hesabınız yok mu?
                <a href="danisman_ol.php" class="font-medium text-orange-500 hover:text-orange-600">
                    Danışman olarak kaydolun
                </a>
            </p>
        </div>

        <?php if ($flash): ?>
            <div class="mt-4 p-4 rounded <?php echo $flash['type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="mt-8 space-y-6">
            <?php echo generateCSRFToken(); ?>
            
            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        E-posta Adresi
                    </label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Şifre
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" 
                           name="remember" 
                           type="checkbox" 
                           class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Beni hatırla
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-orange-500 hover:text-orange-600">
                        Şifremi unuttum
                    </a>
                </div>
            </div>

            <button type="submit" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Giriş Yap
            </button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>