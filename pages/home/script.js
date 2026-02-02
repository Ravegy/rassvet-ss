document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. АНИМАЦИЯ ПОЯВЛЕНИЯ (Hero Section и Bento) ---
    // Находим все элементы, скрытые по умолчанию (anim-hidden)
    const animElements = document.querySelectorAll('.anim-hidden');
    
    if (animElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Добавляем класс, запускающий CSS-анимацию
                    entry.target.classList.add('animate-visible');
                    // Можно перестать следить после появления, если анимация нужна 1 раз
                    observer.unobserve(entry.target); 
                }
            });
        }, { threshold: 0.1 }); // Срабатывает, когда видно 10% элемента

        animElements.forEach(el => observer.observe(el));
    }

    // --- 2. СЛАЙДЕР (Если есть на странице) ---
    const slides = document.querySelectorAll('.slide');
    const container = document.querySelector('.slide-card-container');
    
    if (slides.length > 0) {
        let currentSlide = 0;
        const slideInterval = 5000;

        // Инициализация контейнера
        if (container) {
            setTimeout(() => container.classList.add('active'), 500);
        }

        function nextSlide() {
            // Скрываем текущий
            slides[currentSlide].classList.remove('active');
            
            // Переключаем индекс
            currentSlide = (currentSlide + 1) % slides.length;
            
            // Показываем следующий
            slides[currentSlide].classList.add('active');
            
            // Перезапуск анимации контейнера (если нужно)
            if (container) {
                container.classList.remove('active');
                setTimeout(() => {
                    container.classList.add('active');
                }, 100);
            }
        }

        setInterval(nextSlide, slideInterval);
    }
});