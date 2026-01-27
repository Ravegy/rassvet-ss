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
        }, 8000); 
    }

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

    const scrollElements = document.querySelectorAll('.feature-card, .cat-card, .vin-box');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    scrollElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        
        if (el.classList.contains('cat-card') || el.classList.contains('feature-card')) {
             el.style.transitionDelay = `${(index % 4) * 0.1}s`;
        }

        observer.observe(el);
    });

    if (!document.getElementById('anim-styles')) {
        const styleSheet = document.createElement("style");
        styleSheet.id = 'anim-styles';
        styleSheet.innerText = `
            .visible { opacity: 1 !important; transform: translateY(0) !important; }
        `;
        document.head.appendChild(styleSheet);
    }

    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function (e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
            if (!x[2] && x[1] !== '7') { x[2] = x[1]; x[1] = '7'; } 
            e.target.value = !x[2] ? '+' + (x[1] ? '7' : '') : '+7 (' + x[2] + (x[3] ? ') ' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
        });
    });

    const vinForm = document.querySelector('.vin-form');
    if (vinForm) {
        vinForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const btn = vinForm.querySelector('.form-btn');
            const originalText = btn.textContent;
            
            btn.textContent = 'ОТПРАВЛЕНО!';
            btn.style.background = '#fff';
            btn.style.color = '#000';
            
            setTimeout(() => {
                btn.textContent = originalText;
                btn.style.background = '';
                btn.style.color = '';
                vinForm.reset();
            }, 3000);
        });
    }
});