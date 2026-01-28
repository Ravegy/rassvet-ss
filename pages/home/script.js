document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.slide');
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
    if (slides.length > 0) {
        if (container) setTimeout(() => container.classList.add('active'), 500);
        setInterval(nextSlide, slideInterval);
    }
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
    const vinForm = document.querySelector('.vin-form');
    if (vinForm) {
        vinForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const btn = vinForm.querySelector('.form-btn');
            const originalText = btn.textContent;
            btn.textContent = 'ОТПРАВЛЕНО';
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