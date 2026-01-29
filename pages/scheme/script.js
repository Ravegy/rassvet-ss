document.addEventListener('DOMContentLoaded', () => {
    const viewerContainer = document.getElementById('scheme-viewer-container'); // Основной контейнер
    const wrapper = document.getElementById('scheme-wrapper');
    const img = document.getElementById('scheme-image');
    
    // Кнопки
    const btnZoomIn = document.getElementById('btn-zoom-in');
    const btnZoomOut = document.getElementById('btn-zoom-out');
    const btnReset = document.getElementById('btn-reset');
    const btnFullscreen = document.getElementById('btn-fullscreen'); // Кнопка "На весь экран"
    const btnCloseModal = document.getElementById('btn-close-modal'); // Кнопка "Закрыть"

    // Состояние зума и паннинга
    let scale = 1;
    let pointX = 0; let pointY = 0;
    let startX = 0; let startY = 0;
    let isPanning = false;

    // Ограничения зума
    const minScale = 0.5;
    const maxScale = 5;
    const zoomStep = 0.2;

    function setTransform() {
        img.style.transform = `translate(${pointX}px, ${pointY}px) scale(${scale})`;
    }

    // --- КНОПКИ ЗУМА ---
    btnZoomIn.addEventListener('click', () => {
        scale = Math.min(scale + zoomStep, maxScale);
        setTransform();
    });
    btnZoomOut.addEventListener('click', () => {
        scale = Math.max(scale - zoomStep, minScale);
        setTransform();
    });
    btnReset.addEventListener('click', () => {
        scale = 1; pointX = 0; pointY = 0;
        setTransform();
    });

    // --- ЗУМ КОЛЕСИКОМ ---
    wrapper.addEventListener('wheel', (e) => {
        e.preventDefault();
        const delta = e.deltaY > 0 ? -0.1 : 0.1;
        scale = Math.min(Math.max(scale + delta, minScale), maxScale);
        setTransform();
    });

    // --- ПЕРЕТАСКИВАНИЕ МЫШКОЙ ---
    wrapper.addEventListener('mousedown', (e) => {
        if (e.button !== 0) return; // Только левая кнопка
        e.preventDefault();
        startX = e.clientX - pointX;
        startY = e.clientY - pointY;
        isPanning = true;
        wrapper.style.cursor = 'grabbing';
    });
    document.addEventListener('mousemove', (e) => {
        if (!isPanning) return;
        e.preventDefault();
        pointX = e.clientX - startX;
        pointY = e.clientY - startY;
        setTransform();
    });
    document.addEventListener('mouseup', () => {
        if (isPanning) {
            isPanning = false;
            wrapper.style.cursor = 'grab';
        }
    });

    // --- TOUCH СОБЫТИЯ (МОБИЛЬНЫЙ) ---
    let lastTouchDistance = 0;
    wrapper.addEventListener('touchstart', (e) => {
        if (e.touches.length === 1) {
            startX = e.touches[0].clientX - pointX;
            startY = e.touches[0].clientY - pointY;
            isPanning = true;
        } else if (e.touches.length === 2) {
            isPanning = false;
            lastTouchDistance = getDistance(e.touches);
        }
    });
    wrapper.addEventListener('touchmove', (e) => {
        e.preventDefault();
        if (e.touches.length === 1 && isPanning) {
            pointX = e.touches[0].clientX - startX;
            pointY = e.touches[0].clientY - startY;
            setTransform();
        } else if (e.touches.length === 2) {
            const dist = getDistance(e.touches);
            const delta = dist - lastTouchDistance;
            scale = Math.min(Math.max(scale + (delta * 0.005), minScale), maxScale);
            lastTouchDistance = dist;
            setTransform();
        }
    });
    wrapper.addEventListener('touchend', () => isPanning = false);
    function getDistance(touches) {
        const dx = touches[0].clientX - touches[1].clientX;
        const dy = touches[0].clientY - touches[1].clientY;
        return Math.sqrt(dx * dx + dy * dy);
    }

    // --- ЛОГИКА МОДАЛЬНОГО ОКНА (ПСЕВДО-ФУЛЛСКРИН) ---
    
    // Функция открытия модалки
    function openModal() {
        viewerContainer.classList.add('modal-mode');
        document.body.classList.add('modal-open'); // Блокируем скролл сайта
        // Необязательно: сбрасываем зум при открытии
        // scale = 1; pointX = 0; pointY = 0; setTransform(); 
    }

    // Функция закрытия модалки
    function closeModal() {
        viewerContainer.classList.remove('modal-mode');
        document.body.classList.remove('modal-open'); // Разблокируем скролл
        // Необязательно: сбрасываем зум при закрытии
        scale = 1; pointX = 0; pointY = 0; setTransform();
    }

    // Навешиваем обработчики
    btnFullscreen.addEventListener('click', openModal);
    btnCloseModal.addEventListener('click', closeModal);
    
    // Закрытие по Esc
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && viewerContainer.classList.contains('modal-mode')) {
            closeModal();
        }
    });
});