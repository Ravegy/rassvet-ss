document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('scheme-wrapper');
    const transformLayer = document.getElementById('scheme-transform-layer');
    const schemeImage = document.getElementById('scheme-image');
    
    const btnZoomIn = document.getElementById('btn-zoom-in');
    const btnZoomOut = document.getElementById('btn-zoom-out');
    const btnReset = document.getElementById('btn-reset');
    const btnFullscreen = document.getElementById('btn-fullscreen');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const viewerContainer = document.getElementById('scheme-viewer-container');
    const tableContainer = document.querySelector('.table-responsive');

    let scale = 1, pointX = 0, pointY = 0, startX = 0, startY = 0, isPanning = false;
    const minScale = 0.5, maxScale = 5, zoomStep = 0.2;

    function fitLayerToImage() {
        if (!schemeImage || !transformLayer || !wrapper || schemeImage.naturalWidth === 0) return;

        const wrapW = wrapper.clientWidth;
        const wrapH = wrapper.clientHeight;
        const imgW = schemeImage.naturalWidth;
        const imgH = schemeImage.naturalHeight;
        
        const scaleFactor = Math.min(wrapW / imgW, wrapH / imgH);
        
        const newW = imgW * scaleFactor;
        const newH = imgH * scaleFactor;
        
        transformLayer.style.width = `${newW}px`;
        transformLayer.style.height = `${newH}px`;
    }

    if (schemeImage) {
        if (schemeImage.complete) fitLayerToImage();
        else schemeImage.onload = fitLayerToImage;
    }
    window.addEventListener('resize', fitLayerToImage);

    function setTransform() {
        if(transformLayer) transformLayer.style.transform = `translate(${pointX}px, ${pointY}px) scale(${scale})`;
    }

    if(btnZoomIn) btnZoomIn.addEventListener('click', () => { scale = Math.min(scale + zoomStep, maxScale); setTransform(); });
    if(btnZoomOut) btnZoomOut.addEventListener('click', () => { scale = Math.max(scale - zoomStep, minScale); setTransform(); });
    if(btnReset) btnReset.addEventListener('click', () => { 
        scale = 1; pointX = 0; pointY = 0; 
        fitLayerToImage();
        setTransform(); 
    });

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

    function openModal() { viewerContainer?.classList.add('modal-mode'); document.body.classList.add('modal-open'); fitLayerToImage(); }
    function closeModal() { viewerContainer?.classList.remove('modal-mode'); document.body.classList.remove('modal-open'); scale = 1; pointX = 0; pointY = 0; fitLayerToImage(); setTransform(); }
    btnFullscreen?.addEventListener('click', openModal);
    btnCloseModal?.addEventListener('click', closeModal);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && viewerContainer?.classList.contains('modal-mode')) closeModal(); });

    function highlightPartOnScheme(posCode) {
        document.querySelectorAll('.scheme-marker').forEach(m => m.classList.remove('active'));
        const targetMarkers = document.querySelectorAll(`.scheme-marker[data-pos="${posCode}"]`);
        targetMarkers.forEach(marker => {
            marker.classList.add('active');
        });
    }

    function activateRowByPos(posCode) {
        const rows = document.querySelectorAll('.part-row');
        let targetRow = null;
        rows.forEach(row => {
            const numSpan = row.querySelector('.pos-num');
            if (numSpan && numSpan.innerText.trim() == posCode) targetRow = row;
        });
        if (targetRow) {
            document.querySelectorAll('.part-row.active').forEach(r => r.classList.remove('active'));
            document.querySelectorAll('.part-details-row.active').forEach(r => r.classList.remove('active'));
            targetRow.classList.add('active');
            const details = targetRow.nextElementSibling;
            if (details && details.classList.contains('part-details-row')) details.classList.add('active');
            if (tableContainer) {
                const rowTop = targetRow.offsetTop;
                const containerHeight = tableContainer.clientHeight;
                tableContainer.scrollTo({ top: rowTop - (containerHeight / 2) + 40, behavior: 'smooth' });
            }
        }
    }

    const targetRow = document.getElementById('target-part');
    if (targetRow && tableContainer) {
        setTimeout(() => {
            const rowTop = targetRow.offsetTop;
            const containerHeight = tableContainer.clientHeight;
            tableContainer.scrollTo({ top: rowTop - (containerHeight / 2) + 25, behavior: 'smooth' });
            const posCode = targetRow.querySelector('.pos-num')?.innerText.trim();
            if(posCode) {
                highlightPartOnScheme(posCode);
            }
        }, 500);
    }

    const schemeTable = document.querySelector('.scheme-table');
    if(schemeTable) {
        schemeTable.addEventListener('click', (e) => {
            const row = e.target.closest('.part-row');
            const isInteractive = e.target.closest('.cart-container') || e.target.closest('.heart-container');
            if(row && !isInteractive) {
                const detailsRow = row.nextElementSibling;
                if(detailsRow && detailsRow.classList.contains('part-details-row')) {
                    document.querySelectorAll('.part-details-row.active').forEach(opened => {
                        if(opened !== detailsRow) { opened.classList.remove('active'); opened.previousElementSibling?.classList.remove('active'); }
                    });
                    row.classList.toggle('active');
                    detailsRow.classList.toggle('active');
                    const posNumElement = row.querySelector('.pos-num');
                    if (posNumElement) {
                        const posCode = posNumElement.innerText.trim();
                        highlightPartOnScheme(posCode);
                    }
                }
            }
        });
        
        schemeTable.addEventListener('change', (e) => {
             if(e.target.classList.contains('checkbox')) {
                const checkbox = e.target;
                const cartContainer = checkbox.closest('.cart-container');
                const heartContainer = checkbox.closest('.heart-container');
                
                if (cartContainer) {
                    const art = cartContainer.getAttribute('data-art');
                    const isActive = checkbox.checked; 
                    const action = isActive ? 'add_cart' : 'delete_item';
                    let formData = new FormData();
                    formData.append('action', action);
                    formData.append('article', art);
                    if(isActive) formData.append('qty', 1);
                    fetch('api_actions.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(res => {
                        if(res.status === 'success') {
                            document.querySelectorAll(`.cart-container[data-art="${art}"] .checkbox`).forEach(box => box.checked = isActive);
                            if(typeof updateCartUI === 'function') updateCartUI(res.cart);
                        } else { checkbox.checked = !isActive; }
                    });
                }
                if (heartContainer) {
                    const art = heartContainer.getAttribute('data-art');
                    let formData = new FormData();
                    formData.append('action', 'add_fav');
                    formData.append('article', art);
                    fetch('api_actions.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(res => {
                        if(res.status === 'added') document.querySelectorAll(`.heart-container[data-art="${art}"] .checkbox`).forEach(box => box.checked = true);
                        else if(res.status === 'removed') document.querySelectorAll(`.heart-container[data-art="${art}"] .checkbox`).forEach(box => box.checked = false);
                    });
                }
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        if (typeof IS_ADMIN === 'undefined' || !IS_ADMIN) return;
        if ((e.ctrlKey || e.metaKey) && (e.key === 'e' || e.key === 'у')) {
            e.preventDefault();
            toggleEditMode();
        }
    });

    let isEditMode = false;
    let selectedRow = null;

    function toggleEditMode() {
        isEditMode = !isEditMode;
        if (isEditMode) {
            alert('РЕЖИМ РЕДАКТОРА ВКЛЮЧЕН');
            document.body.classList.add('editor-mode');
            fitLayerToImage();
        } else {
            alert('Режим редактора выключен');
            document.body.classList.remove('editor-mode');
            if(selectedRow) selectedRow.classList.remove('selected-for-edit');
            selectedRow = null;
        }
    }

    if (schemeTable) {
        schemeTable.addEventListener('click', (e) => {
            if (!isEditMode) return;
            const row = e.target.closest('.part-row');
            if (!row) return;
            if (selectedRow) selectedRow.classList.remove('selected-for-edit');
            selectedRow = row;
            selectedRow.classList.add('selected-for-edit');
            e.stopPropagation(); e.preventDefault();
        });
    }

    if (transformLayer) {
        transformLayer.addEventListener('click', (e) => {
            if (isEditMode && selectedRow) {
                const rect = transformLayer.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const percentTop = (y / rect.height) * 100;
                const percentLeft = (x / rect.width) * 100;

                const artElement = selectedRow.querySelector('.part-art');
                if(artElement) {
                    saveCoordinates(artElement.innerText.trim(), percentTop, percentLeft, selectedRow);
                }
                return;
            }
            if (!isEditMode) {
                const marker = e.target.closest('.scheme-marker');
                if (marker) {
                    const posCode = marker.getAttribute('data-pos');
                    highlightPartOnScheme(posCode);
                    activateRowByPos(posCode);
                }
            }
        });

        transformLayer.addEventListener('contextmenu', (e) => {
            if (!isEditMode) return;
            
            const marker = e.target.closest('.scheme-marker');
            if (!marker) return;

            e.preventDefault();

            if (confirm('Удалить эту точку?')) {
                const art = marker.getAttribute('data-art');
                const topVal = marker.style.top;
                const leftVal = marker.style.left;

                const formData = new FormData();
                formData.append('article', art);
                formData.append('x', topVal);
                formData.append('y', leftVal);

                fetch('api_delete_coords.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        marker.remove();
                        if (selectedRow) {
                            selectedRow.classList.remove('selected-for-edit');
                            selectedRow = null;
                        }
                    } else {
                        alert('Ошибка удаления: ' + (data.message || 'Неизвестная ошибка'));
                    }
                });
            }
        });
    }

    function saveCoordinates(art, top, left, row) {
        const formData = new FormData();
        formData.append('article', art);
        formData.append('x', top);
        formData.append('y', left);
        fetch('api_save_coords.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                row.style.background = '#d4edda';
                row.classList.remove('selected-for-edit');
                selectedRow = null;
                const marker = document.createElement('div');
                marker.className = 'scheme-marker temp-marker active';
                marker.style.top = top + '%';
                marker.style.left = left + '%';
                transformLayer.appendChild(marker);
            } else { alert('Ошибка сохранения'); }
        });
    }
});