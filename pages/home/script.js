document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.slide');
    const cardContainer = document.querySelector('.slide-card-container');
    const cardTitle = document.querySelector('.slide-card-title');
    let currentSlide = 0;

    function updateCaption(index) {
        if (!cardContainer || !cardTitle || !slides[index]) return;
        
        cardContainer.classList.remove('active');
        
        setTimeout(() => {
            const text = slides[index].getAttribute('data-caption');
            cardTitle.textContent = text || '';
            cardContainer.classList.add('active');
        }, 400);
    }

    if (slides.length > 0) {
        updateCaption(currentSlide);

        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
            updateCaption(currentSlide);
        }, 8000); // Таймер изменен на 8 секунд
    }

    const burgerBtn = document.getElementById('burger-btn');
    const navMenu = document.getElementById('nav-menu');
    const body = document.body;

    if (burgerBtn && navMenu) {
        burgerBtn.addEventListener('click', () => {
            burgerBtn.classList.toggle('active');
            navMenu.classList.toggle('active');
            if (navMenu.classList.contains('active')) {
                body.style.overflow = 'hidden';
            } else {
                body.style.overflow = '';
            }
        });

        navMenu.querySelectorAll('.nav__link').forEach(link => {
            link.addEventListener('click', () => {
                burgerBtn.classList.remove('active');
                navMenu.classList.remove('active');
                body.style.overflow = '';
            });
        });
    }

    const hero = document.querySelector('.hero__content');
    if (hero) {
        hero.style.opacity = '0';
        hero.style.transform = 'translateY(20px)';
        setTimeout(() => {
            hero.style.transition = 'all 0.8s ease';
            hero.style.opacity = '1';
            hero.style.transform = 'translateY(0)';
        }, 100);
    }
});