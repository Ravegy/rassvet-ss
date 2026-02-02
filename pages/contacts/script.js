document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. АНИМАЦИЯ ПОЯВЛЕНИЯ ---
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-visible');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.tech-card').forEach(card => {
        card.classList.add('animate-hidden');
        observer.observe(card);
    });

    // --- 2. КОПИРОВАНИЕ ТЕКСТА (Email, Телефон) ---
    document.querySelectorAll('.copy-text, .copy-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const textToCopy = btn.getAttribute('data-copy');
            if (textToCopy) {
                navigator.clipboard.writeText(textToCopy).then(() => {
                    const originalHTML = btn.innerHTML;
                    // Меняем иконку на галочку
                    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                    btn.style.color = '#ff3333';
                    
                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                        btn.style.color = '';
                    }, 1500);
                });
            }
        });
    });

    // --- 3. UI ФАЙЛА (Показать имя файла при выборе) ---
    const fileInput = document.getElementById('formFile');
    const fileNameDisplay = document.getElementById('fileName');

    if (fileInput && fileNameDisplay) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileNameDisplay.textContent = this.files[0].name;
                fileNameDisplay.style.color = '#fff';
            } else {
                fileNameDisplay.textContent = 'Прикрепить файл (фото, PDF, DOCX)';
                fileNameDisplay.style.color = '#888';
            }
        });
    }

    // P.S. Логика отправки формы и маска телефона теперь работают
    // автоматически через common.js для всех форм с классом .js-send-form
});