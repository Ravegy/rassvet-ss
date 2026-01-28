document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. АНИМАЦИЯ ПОЯВЛЕНИЯ ПРИ СКРОЛЛЕ ---
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-visible');
            }
        });
    }, { threshold: 0.1 });

    const animTargets = document.querySelectorAll('.tech-card, .price-table tr, .step-item, .faq-item');
    animTargets.forEach(el => {
        el.classList.add('animate-hidden');
        observer.observe(el);
    });

    // --- 2. МАСКА ТЕЛЕФОНА ---
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
            if (!x[2] && x[1] !== '') {
                e.target.value = x[1] === '7' ? '+7 ' : '+7 ' + x[1];
            } else {
                e.target.value = !x[2] ? x[1] : '+7 (' + x[2] + (x[3] ? ') ' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
            }
        });
    });

    // --- 3. РАБОТАЮЩИЙ АККОРДЕОН (FAQ) ---
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        // Ищем заголовок для клика, или кликаем по всему блоку если заголовка нет
        const trigger = item.querySelector('.faq-head') || item;
        
        trigger.addEventListener('click', (e) => {
            // Переключаем класс active
            item.classList.toggle('active');
            
            // Находим скрытый блок с текстом
            const body = item.querySelector('.faq-body');
            
            if (body) {
                if (item.classList.contains('active')) {
                    // Раскрываем: считаем реальную высоту контента (scrollHeight)
                    body.style.maxHeight = body.scrollHeight + "px";
                } else {
                    // Скрываем
                    body.style.maxHeight = null;
                }
            }
        });
    });

    // --- 4. ФОРМА (Визуальная обработка) ---
    const form = document.querySelector('.order-form'); // Поменял класс на order-form (как в s.php)
    if (form) {
        form.addEventListener('submit', (e) => {
            // Здесь отправка через send.php (логику отправки можно оставить в common.js или добавить сюда)
            // Визуальный эффект нажатия:
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            // Если отправка идет через стандартный action, этот код просто для красоты
            // Если через AJAX - нужно e.preventDefault()
        });
    }
});
