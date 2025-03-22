<?php
require_once '../config.php';
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo SITE_TITLE; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4">
                <h1 class="text-2xl font-bold">Admin Panel</h1>
            </div>
            
            <nav class="mt-4">
                <a href="index.php" class="block py-2.5 px-4 hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-gray-700' : ''; ?>">
                    <i class="fas fa-home w-6"></i> Ana Sayfa
                </a>
                
                <!-- Talepler Menüsü -->
                <div class="relative group">
                    <a href="talepler.php" class="block py-2.5 px-4 hover:bg-gray-700 <?php echo strpos($_SERVER['PHP_SELF'], 'talepler') !== false ? 'bg-gray-700' : ''; ?>">
                        <i class="fas fa-clipboard-list w-6"></i> Talepler
                    </a>
                    <div class="hidden group-hover:block absolute left-full top-0 w-48 bg-gray-800">
                        <a href="talepler.php?durum=bekleyen" class="block py-2 px-4 hover:bg-gray-700">Bekleyen Talepler</a>
                        <a href="talepler.php?durum=teklif" class="block py-2 px-4 hover:bg-gray-700">Teklif Verilenler</a>
                        <a href="talepler.php?durum=iptal" class="block py-2 px-4 hover:bg-gray-700">İptal Edilenler</a>
                    </div>
                </div>
                
                <!-- Danışmanlar Menüsü -->
                <div class="relative group">
                    <a href="danismanlar.php" class="block py-2.5 px-4 hover:bg-gray-700 <?php echo strpos($_SERVER['PHP_SELF'], 'danismanlar') !== false ? 'bg-gray-700' : ''; ?>">
                        <i class="fas fa-users w-6"></i> Danışmanlar
                    </a>
                    <div class="hidden group-hover:block absolute left-full top-0 w-48 bg-gray-800">
                        <a href="danismanlar.php?durum=onay_bekleyen" class="block py-2 px-4 hover:bg-gray-700">Onay Bekleyenler</a>
                        <a href="danismanlar.php?durum=aktif" class="block py-2 px-4 hover:bg-gray-700">Aktif Danışmanlar</a>
                        <a href="danismanlar.php?durum=reddedilen" class="block py-2 px-4 hover:bg-gray-700">Reddedilenler</a>
                    </div>
                </div>
                
                <!-- Blog Menüsü -->
                <div class="relative group">
                    <a href="blog.php" class="block py-2.5 px-4 hover:bg-gray-700 <?php echo strpos($_SERVER['PHP_SELF'], 'blog') !== false ? 'bg-gray-700' : ''; ?>">
                        <i class="fas fa-blog w-6"></i> Blog
                    </a>
                    <div class="hidden group-hover:block absolute left-full top-0 w-48 bg-gray-800">
                        <a href="blog_ekle.php" class="block py-2 px-4 hover:bg-gray-700">Blog Ekle</a>
                        <a href="blog.php" class="block py-2 px-4 hover:bg-gray-700">Blog Yazıları</a>
                    </div>
                </div>
                
                <!-- İlanlar Menüsü -->
                <div class="relative group">
                    <a href="ilanlar.php" class="block py-2.5 px-4 hover:bg-gray-700 <?php echo strpos($_SERVER['PHP_SELF'], 'ilanlar') !== false ? 'bg-gray-700' : ''; ?>">
                        <i class="fas fa-building w-6"></i> İlanlar
                    </a>
                    <div class="hidden group-hover:block absolute left-full top-0 w-48 bg-gray-800">
                        <a href="ilan_ekle.php" class="block py-2 px-4 hover:bg-gray-700">Yeni İlan Ekle</a>
                        <a href="ilanlar.php" class="block py-2 px-4 hover:bg-gray-700">Mevcut İlanlar</a>
                    </div>
                </div>
                
                <a href="../logout.php" class="block py-2.5 px-4 hover:bg-gray-700 text-red-400 hover:text-red-300">
                    <i class="fas fa-sign-out-alt w-6"></i> Çıkış Yap
                </a>
            </nav>
        </div>
        
        <!-- Ana İçerik -->
        <div class="flex-1">
            <!-- Üst Bar -->
            <div class="bg-white shadow-md p-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">
                        <?php
                        $page_title = "Ana Sayfa";
                        $current_page = basename($_SERVER['PHP_SELF']);
                        
                        switch ($current_page) {
                            case 'talepler.php':
                                $page_title = "Talepler";
                                break;
                            case 'danismanlar.php':
                                $page_title = "Danışmanlar";
                                break;
                            case 'blog.php':
                                $page_title = "Blog Yazıları";
                                break;
                            case 'blog_ekle.php':
                                $page_title = "Blog Ekle";
                                break;
                            case 'ilanlar.php':
                                $page_title = "İlanlar";
                                break;
                            case 'ilan_ekle.php':
                                $page_title = "Yeni İlan Ekle";
                                break;
                        }
                        
                        echo $page_title;
                        ?>
                    </h2>
                    <div class="flex items-center">
                        <span class="mr-4">
                            <i class="fas fa-user mr-2"></i>
                            <?php echo $_SESSION['user_type']; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Sayfa İçeriği -->
            <div class="p-6">