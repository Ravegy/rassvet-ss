window.updateCartUI = function(cart) {
    const countEls = document.querySelectorAll('.cart-count, .cart-badge');
    const existingOverlay = document.querySelector('.cart-overlay');
    
    let totalQty = 0;
    if (cart && Array.isArray(cart)) {
        cart.forEach(item => totalQty += parseInt(item.qty));
    }

    countEls.forEach(el => {
        el.innerText = totalQty;
        el.style.display = totalQty > 0 ? 'flex' : 'none';
        
        el.classList.remove('pulse-anim');
        void el.offsetWidth;
        el.classList.add('pulse-anim');
    });

    let overlay = existingOverlay;
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'cart-overlay';
        overlay.id = 'cart-overlay';
        document.body.appendChild(overlay);
        
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) overlay.classList.remove('active');
        });
    }

    let html = `
        <div class="cart-modal">
            <div class="cart-header">
                <span class="cart-title">ВАШ ЗАКАЗ (${totalQty})</span>
                <button class="btn-close-cart" id="cart-close">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="cart-body">
    `;

    if (!cart || cart.length === 0) {
        html += `
            <div class="empty-cart-msg">
                <svg class="empty-cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                <div>Ваша корзина пуста</div>
            </div>
        `;
    } else {
        cart.forEach(item => {
            html += `
                <div class="cart-item-row">
                    <div class="c-info">
                        <span class="c-art">${item.part_number}</span>
                        <span class="c-name">${item.name || 'Товар без названия'}</span>
                    </div>
                    <div class="c-controls">
                        <button class="btn-qty btn-minus" data-art="${item.part_number}">−</button>
                        <span class="c-qty-val">${item.qty}</span>
                        <button class="btn-qty btn-plus" data-art="${item.part_number}">+</button>
                    </div>
                    <button class="btn-del-item" data-art="${item.part_number}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
            `;
        });
    }

    html += `</div>`; 

    if (cart && cart.length > 0) {
        html += `
            <div class="cart-footer">
                <div class="cart-total-row">
                    <span>Позиций:</span>
                    <span class="cart-total-val">${cart.length}</span>
                </div>
                <div class="cart-total-row">
                    <span>Всего товаров:</span>
                    <span class="cart-total-val">${totalQty}</span>
                </div>
                <a href="checkout.php" class="btn-order-cart">Оформить заказ</a>
            </div>
        `;
    }

    html += `</div>`;
    overlay.innerHTML = html;

    const closeBtn = overlay.querySelector('#cart-close');
    if(closeBtn) {
        closeBtn.addEventListener('click', () => {
            overlay.classList.remove('active');
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    
    async function apiRequest(action, data = {}) {
        let formData = new FormData();
        formData.append('action', action);
        for(let key in data) formData.append(key, data[key]);

        try {
            let res = await fetch('api_actions.php', { method: 'POST', body: formData });
            return await res.json();
        } catch (e) {
            console.error(e);
            return { status: 'error', message: 'Connection failed' };
        }
    }

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
    }, { threshold: 0.1 });

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
        if (form.classList.contains('static-form')) return;
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;

            btn.innerHTML = 'ОТПРАВКА...';
            btn.style.opacity = '0.7';
            btn.disabled = true;

            try {
                const formData = new FormData(form);
                const response = await fetch('send.php', { method: 'POST', body: formData });
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
                } else { throw new Error(result.message); }
            } catch (error) {
                console.error(error);
                btn.innerHTML = 'ОШИБКА';
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
        
        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
            if (container) {
                container.classList.remove('active');
                setTimeout(() => { container.classList.add('active'); }, 100);
            }
        }
        if (container) setTimeout(() => container.classList.add('active'), 500);
        setInterval(nextSlide, 5000);
    }

    const cartToggle = document.querySelector('.header-cart-btn') || document.getElementById('cart-toggle');
    if(cartToggle) {
        cartToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const overlay = document.querySelector('.cart-overlay');
            if(overlay) overlay.classList.add('active');
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-plus')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.btn-plus');
            apiRequest('update_qty', { article: btn.getAttribute('data-art'), direction: 'plus' })
                .then(res => { if(res.status === 'success') updateCartUI(res.cart); });
        }
        if (e.target.closest('.btn-minus')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.btn-minus');
            apiRequest('update_qty', { article: btn.getAttribute('data-art'), direction: 'minus' })
                .then(res => { if(res.status === 'success') updateCartUI(res.cart); });
        }
        if (e.target.closest('.btn-del-item')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.btn-del-item');
            const row = btn.closest('.cart-item-row');
            if(row) row.style.opacity = '0.5';
            apiRequest('delete_item', { article: btn.getAttribute('data-art') })
                .then(res => { if(res.status === 'success') updateCartUI(res.cart); });
        }
    });

    apiRequest('get_cart').then(res => {
        if(res.status === 'success') updateCartUI(res.cart);
    });

    const addCartBtns = document.querySelectorAll('.btn-add-cart');
    addCartBtns.forEach(btn => {
        if(!btn.classList.contains('scheme-btn')) {
            btn.addEventListener('click', function() {
                let art = this.getAttribute('data-art');
                let originalText = this.innerText;
                this.innerText = '...';
                
                apiRequest('add_cart', { article: art }).then(res => {
                    if(res.status === 'success') {
                        updateCartUI(res.cart);
                        this.innerText = 'OK';
                        this.style.background = '#28a745';
                        setTimeout(() => {
                            this.innerText = originalText;
                            this.style.background = '';
                        }, 1000);
                    }
                });
            });
        }
    });
});