document.addEventListener('DOMContentLoaded', function() {
    const errorBtn = document.querySelector('.btn-error');
    if (errorBtn) {
        setTimeout(function() {
            errorBtn.classList.remove('btn-error');
            // Возвращаем исходный текст из data-атрибута
            errorBtn.innerText = errorBtn.getAttribute('data-original');
        }, 2000);
    }
});