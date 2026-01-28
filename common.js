document.addEventListener('DOMContentLoaded', () => {
    const burger = document.querySelector('.burger-btn');
    const nav = document.querySelector('.nav');

    if (burger && nav) {
        burger.addEventListener('click', (e) => {
            e.stopPropagation();
            burger.classList.toggle('active');
            nav.classList.toggle('active');
        });

        nav.querySelectorAll('.nav__link').forEach(link => {
            link.addEventListener('click', () => {
                burger.classList.remove('active');
                nav.classList.remove('active');
            });
        });

        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target) && !burger.contains(e.target) && nav.classList.contains('active')) {
                burger.classList.remove('active');
                nav.classList.remove('active');
            }
        });
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-visible');
            }
        });
    }, {
        threshold: 0.1
    });

    const animTargets = document.querySelectorAll('.tech-card, .price-table tr, .step-item, .hero-card, .service-card');
    animTargets.forEach(el => {
        el.classList.add('animate-hidden');
        observer.observe(el);
    });

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

    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        item.addEventListener('click', () => {
            item.classList.toggle('active');
        });
    });

    const copyBtns = document.querySelectorAll('.copy-btn, .copy-btn-mini, .copy-text');
    copyBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const textToCopy = btn.getAttribute('data-copy');
            if (textToCopy) {
                navigator.clipboard.writeText(textToCopy).then(() => {
                    const originalHTML = btn.innerHTML;
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

    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;

            btn.innerHTML = 'ОТПРАВКА...';
            btn.style.opacity = '0.7';
            btn.disabled = true;

            try {
                const formData = new FormData(form);
                const response = await fetch('send.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.status === 'success') {
                    btn.innerHTML = 'УСПЕШНО!';
                    btn.style.background = '#fff';
                    btn.style.color = '#000';
                    form.reset();

                    setTimeout(() => {
                        btn.innerHTML = originalText;
                        btn.style.background = '';
                        btn.style.color = '';
                        btn.style.opacity = '1';
                        btn.disabled = false;
                    }, 4000);
                } else {
                    throw new Error(result.message);
                }

            } catch (error) {
                console.error(error);
                btn.innerHTML = 'ОШИБКА';
                // Изменил фон ошибки на красный (был #ff3333, оставил таким же, так как это красный)
                btn.style.background = '#ff3333';
                btn.style.color = '#fff';

                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.background = '';
                    btn.style.color = '';
                    btn.style.opacity = '1';
                    btn.disabled = false;
                }, 3000);
            }
        });
    });
    
    const slides = document.querySelectorAll('.slide');
    if (slides.length > 0) {
        const container = document.querySelector('.slide-card-container');
        let currentSlide = 0;
        const slideInterval = 5000;

        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
            
            if (container) {
                container.classList.remove('active');
                setTimeout(() => {
                    container.classList.add('active');
                }, 100);
            }
        }
        
        if (container) setTimeout(() => container.classList.add('active'), 500);
        setInterval(nextSlide, slideInterval);
    }
});