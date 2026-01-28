document.addEventListener('DOMContentLoaded', () => {
    const copyBtn = document.querySelector('.btn-download');
    if (copyBtn) {
        copyBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const requisites = `ООО "РАССВЕТ-С"\nИНН: 7805626388\nКПП: 780501001\nОГРН: 1137847277873\nАдрес: 198095, г. Санкт-Петербург, ул. Промышленная, д. 42`;
            navigator.clipboard.writeText(requisites).then(() => {
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = 'СКОПИРОВАНО В БУФЕР';
                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                }, 2000);
            });
        });
    }
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-visible');
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.tech-card, .partner-card, .lf-item').forEach(el => {
        el.classList.add('animate-hidden');
        observer.observe(el);
    });
});