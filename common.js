// --- ГЛОБАЛЬНЫЕ ФУНКЦИИ (Доступны везде) ---

function updateCartUI(cartData) {
    const countEl = document.getElementById('cart-count');
    const listEl = document.getElementById('cart-items');
    
    let total = 0;
    if(Array.isArray(cartData)) {
        cartData.forEach(item => total += parseInt(item.qty));
    }
    if(countEl) countEl.innerText = total;

    if(listEl) {
        listEl.innerHTML = '';
        
        if(!cartData || cartData.length === 0) {
            listEl.innerHTML = '<div class="cart-empty">Ваша корзина пуста</div>';
        } else {
            cartData.forEach(item => {
                let row = document.createElement('div');
                row.className = 'cart-item-row';
                
                row.innerHTML = `
                    <div class="c-info">
                        <span class="c-art">${item.part_number}</span>
                        <span class="c-name">${item.name || 'Без названия'}</span>
                    </div>
                    
                    <div class="c-controls">
                        <button class="btn-qty btn-minus" data-art="${item.part_number}">−</button>
                        <span class="c-qty-val">${item.qty}</span>
                        <button class="btn-qty btn-plus" data-art="${item.part_number}">+</button>
                    </div>

                    <button class="btn-del-item" data-art="${item.part_number}" title="Удалить">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                `;
                listEl.appendChild(row);
            });
        }
    }
}

// --- ОСНОВНОЙ КОД ---
document.addEventListener('DOMContentLoaded', () => {
    
    // Вспомогательная функция для запросов внутри common.js
    async function apiRequest(action, data = {}) {
        let formData = new FormData();
        formData.append('action', action);
        for(let key in data) formData.append(key, data[key]);

        try {
            let res = await fetch('api_actions.php', { method: 'POST', body: formData });
            return await res.json();
        } catch (e) {
            console.error('API Error:', e);
            return { status: 'error', message: 'Connection failed' };
        }
    }

    // 1. БУРГЕР МЕНЮ
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

    // 2. АНИМАЦИИ
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

    // 3. МАСКА ТЕЛЕФОНА
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

    // 4. FAQ
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        item.addEventListener('click', () => {
            item.classList.toggle('active');
        });
    });

    // 5. КОПИРОВАНИЕ
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

    // 6. ОТПРАВКА ФОРМ
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
    
    // 7. СЛАЙДЕР
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

    // 8. КОРЗИНА (Оверлей и Кнопки внутри корзины)
    const cartOverlay = document.getElementById('cart-overlay');
    const cartToggle = document.getElementById('cart-toggle');
    const cartClose = document.getElementById('cart-close');
    const cartItemsContainer = document.getElementById('cart-items');

    if(cartToggle && cartOverlay) {
        cartToggle.addEventListener('click', (e) => {
            e.preventDefault();
            cartOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }

    function closeCart() {
        if(cartOverlay) {
            cartOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    if(cartClose) cartClose.addEventListener('click', closeCart);
    if(cartOverlay) {
        cartOverlay.addEventListener('click', (e) => { if (e.target === cartOverlay) closeCart(); });
    }
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && cartOverlay && cartOverlay.classList.contains('active')) closeCart();
    });

    if (cartItemsContainer) {
        cartItemsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.btn-plus')) {
                const btn = e.target.closest('.btn-plus');
                apiRequest('update_qty', { article: btn.getAttribute('data-art'), direction: 'plus' })
                    .then(res => { if(res.status === 'success') updateCartUI(res.cart); });
            }
            if (e.target.closest('.btn-minus')) {
                const btn = e.target.closest('.btn-minus');
                apiRequest('update_qty', { article: btn.getAttribute('data-art'), direction: 'minus' })
                    .then(res => { if(res.status === 'success') updateCartUI(res.cart); });
            }
            if (e.target.closest('.btn-del-item')) {
                const btn = e.target.closest('.btn-del-item');
                btn.closest('.cart-item-row').style.opacity = '0.5';
                apiRequest('delete_item', { article: btn.getAttribute('data-art') })
                    .then(res => { if(res.status === 'success') updateCartUI(res.cart); });
            }
        });
    }

    // 9. ИНИЦИАЛИЗАЦИЯ КОРЗИНЫ
    apiRequest('get_cart').then(res => {
        if(res.status === 'success') updateCartUI(res.cart);
    });

    // 10. КНОПКИ ДОБАВЛЕНИЯ (Обычные, не на схеме)
    const addCartBtns = document.querySelectorAll('.btn-add-cart');
    addCartBtns.forEach(btn => {
        // Проверяем, что это не кнопка схемы (у них своя логика)
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