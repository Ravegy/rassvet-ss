document.addEventListener('DOMContentLoaded', () => {
    
    // --- АНИМАЦИЯ ПОЯВЛЕНИЯ ПРИ СКРОЛЛЕ ---
    // Оставляем здесь, так как на этой странице специфичный набор элементов
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-visible');
            }
        });
    }, { threshold: 0.1 });

    // Список элементов для анимации на странице сервиса
    const animTargets = document.querySelectorAll('.tech-card, .price-table tr, .step-item, .faq-item');
    
    animTargets.forEach(el => {
        el.classList.add('animate-hidden');
        observer.observe(el);
    });

    // Все остальные функции (Формы, Телефоны, Аккордеон) теперь работают через common.js
});