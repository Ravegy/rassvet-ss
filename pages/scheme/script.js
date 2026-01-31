document.addEventListener('DOMContentLoaded', () => {
    // === ВЬЮВЕР СХЕМЫ (ЗУМ, ПЕРЕМЕЩЕНИЕ) ===
    const wrapper = document.getElementById('scheme-wrapper');
    const img = document.getElementById('scheme-image');
    const btnZoomIn = document.getElementById('btn-zoom-in');
    const btnZoomOut = document.getElementById('btn-zoom-out');
    const btnReset = document.getElementById('btn-reset');
    const btnFullscreen = document.getElementById('btn-fullscreen');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const viewerContainer = document.getElementById('scheme-viewer-container');

    let scale = 1, pointX = 0, pointY = 0, startX = 0, startY = 0, isPanning = false;
    const minScale = 0.5, maxScale = 5, zoomStep = 0.2;

    function setTransform() { if(img) img.style.transform = `translate(${pointX}px, ${pointY}px) scale(${scale})`; }

    if(btnZoomIn) btnZoomIn.addEventListener('click', () => { scale = Math.min(scale + zoomStep, maxScale); setTransform(); });
    if(btnZoomOut) btnZoomOut.addEventListener('click', () => { scale = Math.max(scale - zoomStep, minScale); setTransform(); });
    if(btnReset) btnReset.addEventListener('click', () => { scale = 1; pointX = 0; pointY = 0; setTransform(); });

    if(wrapper) {
        wrapper.addEventListener('wheel', (e) => {
            e.preventDefault();
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            scale = Math.min(Math.max(scale + delta, minScale), maxScale);
            setTransform();
        });
        wrapper.addEventListener('mousedown', (e) => {
            if (e.button !== 0) return;
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
            if (isPanning) { isPanning = false; if(wrapper) wrapper.style.cursor = 'grab'; }
        });
        let lastTouchDistance = 0;
        wrapper.addEventListener('touchstart', (e) => {
            if (e.touches.length === 1) { startX = e.touches[0].clientX - pointX; startY = e.touches[0].clientY - pointY; isPanning = true; }
            else if (e.touches.length === 2) { isPanning = false; lastTouchDistance = Math.hypot(e.touches[0].clientX - e.touches[1].clientX, e.touches[0].clientY - e.touches[1].clientY); }
        });
        wrapper.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (e.touches.length === 1 && isPanning) { pointX = e.touches[0].clientX - startX; pointY = e.touches[0].clientY - startY; setTransform(); }
            else if (e.touches.length === 2) {
                const dist = Math.hypot(e.touches[0].clientX - e.touches[1].clientX, e.touches[0].clientY - e.touches[1].clientY);
                const delta = dist - lastTouchDistance;
                scale = Math.min(Math.max(scale + (delta * 0.005), minScale), maxScale);
                lastTouchDistance = dist;
                setTransform();
            }
        });
        wrapper.addEventListener('touchend', () => { isPanning = false; });
    }

    function openModal() { viewerContainer?.classList.add('modal-mode'); document.body.classList.add('modal-open'); }
    function closeModal() { viewerContainer?.classList.remove('modal-mode'); document.body.classList.remove('modal-open'); scale = 1; pointX = 0; pointY = 0; setTransform(); }
    btnFullscreen?.addEventListener('click', openModal);
    btnCloseModal?.addEventListener('click', closeModal);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && viewerContainer?.classList.contains('modal-mode')) closeModal(); });

    // Автоскролл
    const targetRow = document.getElementById('target-part');
    const tableContainer = document.querySelector('.table-responsive');
    if (targetRow && tableContainer) {
        setTimeout(() => {
            const rowTop = targetRow.offsetTop;
            const containerHeight = tableContainer.clientHeight;
            tableContainer.scrollTo({ top: rowTop - (containerHeight / 2) + 25, behavior: 'smooth' });
        }, 300);
    }

    // === ЛОГИКА ТАБЛИЦЫ ===
    const schemeTable = document.querySelector('.scheme-table');
    if(schemeTable) {
        
        // 1. Делегирование для КЛИКОВ (Аккордеон)
        schemeTable.addEventListener('click', (e) => {
            // Раскрытие строки (если клик НЕ по кнопкам/чекбоксам)
            const row = e.target.closest('.part-row');
            const isInteractive = e.target.closest('.cart-container') || e.target.closest('.heart-container');
            
            if(row && !isInteractive) {
                const detailsRow = row.nextElementSibling;
                if(detailsRow && detailsRow.classList.contains('part-details-row')) {
                    document.querySelectorAll('.part-details-row.active').forEach(opened => {
                        if(opened !== detailsRow) {
                            opened.classList.remove('active');
                            opened.previousElementSibling?.classList.remove('active');
                        }
                    });
                    row.classList.toggle('active');
                    detailsRow.classList.toggle('active');
                }
            }
        });

        // 2. Делегирование для ИЗМЕНЕНИЯ ЧЕКБОКСОВ (Корзина и Избранное)
        schemeTable.addEventListener('change', (e) => {
            if(e.target.classList.contains('checkbox')) {
                const checkbox = e.target;
                
                // Проверяем, какой это контейнер
                const cartContainer = checkbox.closest('.cart-container');
                const heartContainer = checkbox.closest('.heart-container');
                
                // --- ЛОГИКА КОРЗИНЫ ---
                if (cartContainer) {
                    const art = cartContainer.getAttribute('data-art');
                    const isActive = checkbox.checked; // true - добавили, false - убрали
                    const action = isActive ? 'add_cart' : 'delete_item';

                    let formData = new FormData();
                    formData.append('action', action);
                    formData.append('article', art);
                    if(isActive) formData.append('qty', 1);

                    fetch('api_actions.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(res => {
                        if(res.status === 'success') {
                            // Синхронизируем ВСЕ кнопки корзины с этим артикулом на странице
                            document.querySelectorAll(`.cart-container[data-art="${art}"] .checkbox`)
                                .forEach(box => box.checked = isActive);
                            
                            // Обновляем шапку
                            if(typeof updateCartUI === 'function') updateCartUI(res.cart);
                        } else {
                            // Ошибка - возвращаем галочку назад
                            alert('Ошибка корзины');
                            checkbox.checked = !isActive;
                        }
                    })
                    .catch(err => { console.error(err); checkbox.checked = !isActive; });
                }

                // --- ЛОГИКА ИЗБРАННОГО ---
                if (heartContainer) {
                    const art = heartContainer.getAttribute('data-art');
                    
                    let formData = new FormData();
                    formData.append('action', 'add_fav');
                    formData.append('article', art);
                    
                    fetch('api_actions.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(res => {
                        if(res.status === 'added') {
                             document.querySelectorAll(`.heart-container[data-art="${art}"] .checkbox`)
                                .forEach(box => box.checked = true);
                        } else if(res.status === 'removed') {
                             document.querySelectorAll(`.heart-container[data-art="${art}"] .checkbox`)
                                .forEach(box => box.checked = false);
                        } else if(res.message === 'Нужна авторизация') {
                            alert('Войдите, чтобы добавить в избранное');
                            checkbox.checked = !checkbox.checked;
                        }
                    })
                    .catch(err => { console.error(err); checkbox.checked = !checkbox.checked; });
                }
            }
        });
    }
});