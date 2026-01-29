document.addEventListener('DOMContentLoaded', () => {
    const viewerContainer = document.getElementById('scheme-viewer-container');
    const wrapper = document.getElementById('scheme-wrapper');
    const img = document.getElementById('scheme-image');
    
    // Элементы управления
    const btnZoomIn = document.getElementById('btn-zoom-in');
    const btnZoomOut = document.getElementById('btn-zoom-out');
    const btnReset = document.getElementById('btn-reset');
    const btnFullscreen = document.getElementById('btn-fullscreen');
    const btnCloseModal = document.getElementById('btn-close-modal');

    // Переменные состояния
    let scale = 1;
    let pointX = 0; 
    let pointY = 0;
    let startX = 0; 
    let startY = 0;
    let isPanning = false;

    // Константы зума
    const minScale = 0.5;
    const maxScale = 5;
    const zoomStep = 0.2;

    // Функция применения трансформации
    function setTransform() {
        img.style.transform = `translate(${pointX}px, ${pointY}px) scale(${scale})`;
    }

    // --- КНОПКИ ЗУМА ---
    if(btnZoomIn) {
        btnZoomIn.addEventListener('click', () => {
            scale = Math.min(scale + zoomStep, maxScale);
            setTransform();
        });
    }
    if(btnZoomOut) {
        btnZoomOut.addEventListener('click', () => {
            scale = Math.max(scale - zoomStep, minScale);
            setTransform();
        });
    }
    if(btnReset) {
        btnReset.addEventListener('click', () => {
            scale = 1; pointX = 0; pointY = 0;
            setTransform();
        });
    }

    // --- ЗУМ КОЛЕСИКОМ ---
    if(wrapper) {
        wrapper.addEventListener('wheel', (e) => {
            e.preventDefault();
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            scale = Math.min(Math.max(scale + delta, minScale), maxScale);
            setTransform();
        });

        // --- ПЕРЕТАСКИВАНИЕ МЫШКОЙ ---
        wrapper.addEventListener('mousedown', (e) => {
            if (e.button !== 0) return; // Только ЛКМ
            e.preventDefault();
            startX = e.clientX - pointX;
            startY = e.clientY - pointY;
            isPanning = true;
            wrapper.style.cursor = 'grabbing';
        });

        // --- TOUCH СОБЫТИЯ (МОБИЛЬНЫЙ) ---
        let lastTouchDistance = 0;
        
        wrapper.addEventListener('touchstart', (e) => {
            if (e.touches.length === 1) {
                // Один палец - драг
                startX = e.touches[0].clientX - pointX;
                startY = e.touches[0].clientY - pointY;
                isPanning = true;
            } else if (e.touches.length === 2) {
                // Два пальца - зум
                isPanning = false;
                lastTouchDistance = getDistance(e.touches);
            }
        });

        wrapper.addEventListener('touchmove', (e) => {
            e.preventDefault(); // Блокируем скролл страницы
            
            if (e.touches.length === 1 && isPanning) {
                pointX = e.touches[0].clientX - startX;
                pointY = e.touches[0].clientY - startY;
                setTransform();
            } else if (e.touches.length === 2) {
                const dist = getDistance(e.touches);
                const delta = dist - lastTouchDistance;
                // Коэффициент чувствительности зума
                scale = Math.min(Math.max(scale + (delta * 0.005), minScale), maxScale);
                lastTouchDistance = dist;
                setTransform();
            }
        });

        wrapper.addEventListener('touchend', () => {
            isPanning = false;
        });
    }

    // Слушатели на документ для мыши (чтобы не терять фокус при выходе за пределы блока)
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
            if(wrapper) wrapper.style.cursor = 'grab';
        }
    });

    function getDistance(touches) {
        const dx = touches[0].clientX - touches[1].clientX;
        const dy = touches[0].clientY - touches[1].clientY;
        return Math.sqrt(dx * dx + dy * dy);
    }

    // --- МОДАЛЬНОЕ ОКНО ---
    function openModal() {
        if(viewerContainer) {
            viewerContainer.classList.add('modal-mode');
            document.body.classList.add('modal-open');
        }
    }

    function closeModal() {
        if(viewerContainer) {
            viewerContainer.classList.remove('modal-mode');
            document.body.classList.remove('modal-open');
            // Сброс зума при закрытии (опционально)
            scale = 1; pointX = 0; pointY = 0; setTransform();
        }
    }

    if(btnFullscreen) btnFullscreen.addEventListener('click', openModal);
    if(btnCloseModal) btnCloseModal.addEventListener('click', closeModal);

    // Закрытие по ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && viewerContainer && viewerContainer.classList.contains('modal-mode')) {
            closeModal();
        }
    });

    // --- АВТОСКРОЛЛ К ПОДСВЕЧЕННОЙ ДЕТАЛИ ---
    const targetRow = document.getElementById('target-part');
    const tableContainer = document.querySelector('.table-responsive');

    if (targetRow && tableContainer) {
        setTimeout(() => {
            const rowTop = targetRow.offsetTop;
            const containerHeight = tableContainer.clientHeight;
            // Скроллим так, чтобы строка была примерно по центру
            tableContainer.scrollTo({
                top: rowTop - (containerHeight / 2) + 25, 
                behavior: 'smooth'
            });
        }, 300);
    }
});