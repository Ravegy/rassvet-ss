document.addEventListener('DOMContentLoaded', () => {
    const observerOptions = {
        threshold: 0.1
    };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                if (entry.target.classList.contains('custom-list')) {
                    const items = entry.target.querySelectorAll('li');
                    items.forEach((item, index) => {
                        item.style.transitionDelay = `${index * 0.1}s`;
                        item.classList.add('visible');
                    });
                }
            }
        });
    }, observerOptions);
    document.querySelectorAll('.tech-card, .srv-item, .p-item, .stat-card').forEach(el => {
        el.classList.add('animate-hidden');
        observer.observe(el);
    });
});