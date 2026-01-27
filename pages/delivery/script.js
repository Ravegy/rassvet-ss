document.addEventListener('DOMContentLoaded', () => {

    // --- 1. МОБИЛЬНОЕ МЕНЮ ---
    const burgerBtn = document.getElementById('burger-btn');
    const navMenu = document.getElementById('nav-menu');
    const body = document.body;

    if (burgerBtn && navMenu) {
        burgerBtn.addEventListener('click', () => {
            burgerBtn.classList.toggle('active');
            navMenu.classList.toggle('active');
            body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
        });
        navMenu.querySelectorAll('.nav__link').forEach(link => {
            link.addEventListener('click', () => {
                burgerBtn.classList.remove('active');
                navMenu.classList.remove('active');
                body.style.overflow = '';
            });
        });
    }

    // --- 2. АНИМАЦИЯ ПОЯВЛЕНИЯ (SCROLL REVEAL) ---
    
    // Элементы, которые будем анимировать
    const elementsToAnimate = document.querySelectorAll(
        '.finance-left, .company-details-box, .logistics-panel, .partners-panel, .order-list li, .log-feature-card, .partner-box'
    );

    // Добавляем начальный класс скрытия всем элементам
    elementsToAnimate.forEach(el => {
        el.classList.add('animate-hidden');
    });

    const observerOptions = {
        threshold: 0.1, // Элемент считается видимым, когда показался на 10%
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Добавляем класс видимости
                entry.target.classList.add('animate-visible');
                observer.unobserve(entry.target); // Перестаем следить после появления
            }
        });
    }, observerOptions);

    // Настраиваем задержки (Stagger Effect)
    elementsToAnimate.forEach((el) => {
        // Вычисляем задержку на основе индекса в родителе
        let delay = 0;

        // Для списка "Порядок работы"
        if (el.tagName === 'LI' && el.parentElement.classList.contains('order-list')) {
            const index = Array.from(el.parentElement.children).indexOf(el);
            delay = index * 0.15;
        }
        // Для карточек логистики
        else if (el.classList.contains('log-feature-card')) {
            const index = Array.from(el.parentElement.children).indexOf(el);
            delay = index * 0.2;
        }
        // Для партнеров
        else if (el.classList.contains('partner-box')) {
            const index = Array.from(el.parentElement.children).indexOf(el);
            delay = index * 0.1;
        }

        el.style.transitionDelay = `${delay}s`;
        observer.observe(el);
    });


    // --- 3. КОПИРОВАНИЕ РЕКВИЗИТОВ ---
    const copyElements = document.querySelectorAll('.cd-value');
    
    if (copyElements.length > 0) {
        copyElements.forEach(el => {
            el.style.cursor = 'pointer';
            el.title = "Нажмите, чтобы скопировать";
            
            // Ховер эффект
            el.addEventListener('mouseenter', () => { el.style.color = 'var(--yellow)'; });
            el.addEventListener('mouseleave', () => { 
                if (el.dataset.copied !== 'true') el.style.color = '#fff'; 
            });

            // Клик эффект
            el.addEventListener('click', async () => {
                const text = el.innerText;
                try {
                    await navigator.clipboard.writeText(text);
                    
                    el.dataset.copied = 'true';
                    const originalText = text;
                    
                    // Зеленый цвет успеха
                    el.style.color = '#4cd964';
                    el.innerText = 'СКОПИРОВАНО';
                    el.style.fontWeight = '900';
                    
                    setTimeout(() => {
                        el.style.color = '#fff';
                        el.innerText = originalText;
                        el.style.fontWeight = '700';
                        el.dataset.copied = 'false';
                    }, 1200);
                    
                } catch (err) {
                    console.error('Ошибка копирования', err);
                }
            });
        });
    }
});