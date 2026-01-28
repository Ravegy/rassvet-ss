document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Анимация появления
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

    // 2. Логика копирования
    const copyBtns = document.querySelectorAll('.copy-btn, .copy-btn-mini, .copy-text');
    
    copyBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const textToCopy = btn.getAttribute('data-copy');
            if (textToCopy) {
                navigator.clipboard.writeText(textToCopy).then(() => {
                    const originalHTML = btn.innerHTML;
                    
                    // Меняем иконку на галочку
                    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                    // Изменил желтый цвет на красный
                    btn.style.color = '#ff3333'; 

                    setTimeout(() => {
                        btn.innerHTML = originalHTML;
                        btn.style.color = '';
                    }, 1500);
                });
            }
        });
    });

    // 3. Форма и 4. Маска телефона (без изменений)
    const form = document.querySelector('.contact-form');
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'ОТПРАВЛЕНО';
            btn.style.background = '#fff';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '';
                form.reset();
            }, 3000);
        });
    }
    const phoneInput = document.querySelector('input[type="tel"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', (e) => {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
            if (!x[2] && x[1] !== '') {
                e.target.value = x[1] === '7' ? '+7 ' : '+7 ' + x[1];
            } else {
                e.target.value = !x[2] ? x[1] : '+7 (' + x[2] + (x[3] ? ') ' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
            }
        });
    }
});