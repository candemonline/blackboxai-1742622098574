</main>

    <footer class="bg-gray-800 text-white mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Arazi Değerleme</h3>
                    <p class="text-gray-400">
                        Profesyonel arazi değerleme ve danışmanlık hizmetleri sunuyoruz.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Hızlı Erişim</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-400 hover:text-white">Anasayfa</a></li>
                        <li><a href="/hakkimizda.php" class="text-gray-400 hover:text-white">Hakkımızda</a></li>
                        <li><a href="/blog.php" class="text-gray-400 hover:text-white">Blog</a></li>
                        <li><a href="/iletisim.php" class="text-gray-400 hover:text-white">İletişim</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">İletişim</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>
                            <i class="fas fa-phone mr-2"></i>
                            <a href="tel:+902121234567" class="hover:text-white">0212 123 45 67</a>
                        </li>
                        <li>
                            <i class="fas fa-envelope mr-2"></i>
                            <a href="mailto:info@arazidegerleme.com" class="hover:text-white">info@arazidegerleme.com</a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            İstanbul, Türkiye
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Sosyal Medya</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <i class="fab fa-linkedin-in text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> Arazi Değerleme. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobil menü toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Flash mesajlarını otomatik gizle
        const flashMessage = document.querySelector('.flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.opacity = '0';
                setTimeout(() => {
                    flashMessage.remove();
                }, 300);
            }, 3000);
        }
    </script>
</body>
</html>