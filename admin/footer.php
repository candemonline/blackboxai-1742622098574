</div> <!-- Sayfa İçeriği Sonu -->
        </div> <!-- Ana İçerik Sonu -->
    </div> <!-- Min-height-screen Flex Sonu -->

    <!-- Genel JavaScript -->
    <script>
        // Sidebar menü hover efekti
        document.querySelectorAll('.group').forEach(group => {
            const submenu = group.querySelector('.group-hover\\:block');
            group.addEventListener('mouseenter', () => {
                submenu.style.display = 'block';
            });
            group.addEventListener('mouseleave', () => {
                submenu.style.display = 'none';
            });
        });

        // Flash mesajları için otomatik kapanma
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(message => {
            setTimeout(() => {
                message.style.opacity = '0';
                setTimeout(() => {
                    message.remove();
                }, 300);
            }, 3000);
        });

        // Form doğrulama için genel fonksiyon
        function validateForm(form) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            return isValid;
        }

        // Dosya yükleme önizleme
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }

        // Onay kutusu için genel fonksiyon
        function confirmAction(message) {
            return confirm(message || 'Bu işlemi gerçekleştirmek istediğinizden emin misiniz?');
        }

        // AJAX post işlemi için genel fonksiyon
        async function postData(url, data) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                return await response.json();
            } catch (error) {
                console.error('Hata:', error);
                return { success: false, message: 'Bir hata oluştu.' };
            }
        }

        // Tablo sıralama için genel fonksiyon
        function sortTable(table, column) {
            const rows = Array.from(table.querySelectorAll('tr:not(:first-child)'));
            const isNumeric = !isNaN(rows[0].children[column].textContent);
            rows.sort((a, b) => {
                const aValue = a.children[column].textContent;
                const bValue = b.children[column].textContent;
                if (isNumeric) {
                    return parseFloat(aValue) - parseFloat(bValue);
                }
                return aValue.localeCompare(bValue);
            });
            rows.forEach(row => table.appendChild(row));
        }

        // Arama filtresi için genel fonksiyon
        function filterTable(input, table) {
            const filter = input.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');
            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const text = cell.textContent || cell.innerText;
                        if (text.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                rows[i].style.display = found ? '' : 'none';
            }
        }
    </script>
</body>
</html>