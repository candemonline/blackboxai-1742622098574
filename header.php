<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/">
                            <img class="h-12 w-auto" src="/assets/images/logo.png" alt="<?php echo SITE_TITLE; ?>">
                        </a>
                    </div>
                </div>

                <div class="flex items-center">
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="/" class="text-gray-600 hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium">
                            Anasayfa
                        </a>
                        <a href="/about-us.php" class="text-gray-600 hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium">
                            Hakkımızda
                        </a>
                        <a href="/blog.php" class="text-gray-600 hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium">
                            Blog
                        </a>
                        <a href="/contact.php" class="text-gray-600 hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium">
                            İletişim
                        </a>
                        <?php if (isLoggedIn()): ?>
                            <a href="/ilanlar.php" class="text-gray-600 hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium">
                                İlanlar
                            </a>
                            <?php if (isAdmin()): ?>
                                <a href="/admin" class="bg-orange-500 text-white hover:bg-orange-600 px-3 py-2 rounded-md text-sm font-medium">
                                    Yönetim Paneli
                                </a>
                            <?php endif; ?>
                            <div class="relative group">
                                <button class="text-gray-600 group-hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium">
                                    <?php echo e($_SESSION['user_name']); ?>
                                    <i class="fas fa-chevron-down ml-1"></i>
                                </button>
                                <div class="absolute right-0 w-48 mt-2 py-2 bg-white rounded-md shadow-xl z-10 hidden group-hover:block">
                                    <a href="/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-500 hover:text-white">
                                        Çıkış Yap
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="/danisman_ol.php" class="text-gray-600 hover:text-orange-500 px-3 py-2 rounded-md text-sm font-medium">
                                Danışman Ol
                            </a>
                            <a href="/login.php" class="bg-orange-500 text-white hover:bg-orange-600 px-3 py-2 rounded-md text-sm font-medium">
                                Giriş Yap
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php 
        $flash = getFlashMessage();
        if ($flash): 
        ?>
            <div class="mb-4 p-4 rounded <?php echo $flash['type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>