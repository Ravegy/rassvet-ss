document.addEventListener('DOMContentLoaded', () => {
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
    }, {
        threshold: 0.1
    });

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
                const response = await fetch('send.php', {
                    method: 'POST',
                    body: formData
                });

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
                } else {
                    throw new Error(result.message);
                }

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
        
        if (container) setTimeout(() => container.classList.add('active'), 500);
        setInterval(nextSlide, slideInterval);
    }

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

    const cartOverlay = document.getElementById('cart-overlay');
    const cartToggle = document.getElementById('cart-toggle');
    const cartClose = document.getElementById('cart-close');

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
        cartOverlay.addEventListener('click', (e) => {
            if (e.target === cartOverlay) {
                closeCart();
            }
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && cartOverlay && cartOverlay.classList.contains('active')) {
            closeCart();
        }
    });

    const cartItemsContainer = document.getElementById('cart-items');
    if (cartItemsContainer) {
        cartItemsContainer.addEventListener('click', function(e) {
            
            if (e.target.closest('.btn-plus')) {
                const btn = e.target.closest('.btn-plus');
                const art = btn.getAttribute('data-art');
                apiRequest('update_qty', { article: art, direction: 'plus' }).then(res => {
                    if(res.status === 'success') updateCartUI(res.cart);
                });
            }

            if (e.target.closest('.btn-minus')) {
                const btn = e.target.closest('.btn-minus');
                const art = btn.getAttribute('data-art');
                apiRequest('update_qty', { article: art, direction: 'minus' }).then(res => {
                    if(res.status === 'success') updateCartUI(res.cart);
                });
            }

            if (e.target.closest('.btn-del-item')) {
                const btn = e.target.closest('.btn-del-item');
                const art = btn.getAttribute('data-art');
                
                const row = btn.closest('.cart-item-row');
                row.style.opacity = '0.5';

                apiRequest('delete_item', { article: art }).then(res => {
                    if(res.status === 'success') updateCartUI(res.cart);
                });
            }
        });
    }

    apiRequest('get_cart').then(res => {
        if(res.status === 'success') updateCartUI(res.cart);
    });

    const addCartBtns = document.querySelectorAll('.btn-add-cart');
    addCartBtns.forEach(btn => {
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
    });

    const partsTable = document.querySelector('.parts-table');
    if(partsTable) {
        partsTable.addEventListener('click', function(e) {
            const tr = e.target.closest('tr');
            if(!tr || tr.querySelector('th')) return;

            if(e.target.closest('.row-actions-overlay')) return;

            document.querySelectorAll('.parts-table tr.active').forEach(r => r.classList.remove('active'));
            document.querySelectorAll('.row-actions-overlay').forEach(el => el.remove());

            tr.classList.add('active');

            const artCell = tr.querySelector('.art-cell span');
            if(!artCell) return;
            const art = artCell.innerText.trim();

            const actionsDiv = document.createElement('div');
            actionsDiv.className = 'row-actions-overlay';
            actionsDiv.innerHTML = `
                <div class="btn-mini-action btn-add-mini" title="В корзину">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                </div>
                <div class="btn-mini-action btn-fav-mini" title="В избранное">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.001 4.529c2.349-2.109 5.979-2.039 8.242.228 2.262 2.268 2.34 5.88.236 8.236l-8.48 8.492-8.478-8.492c-2.104-2.356-2.025-5.974.236-8.236 2.265-2.264 5.888-2.34 8.244-.228z"/></svg>
                </div>
            `;

            const lastCell = tr.querySelector('td:last-child');
            if(lastCell) {
                lastCell.style.position = 'relative';
                lastCell.appendChild(actionsDiv);
            }

            actionsDiv.querySelector('.btn-add-mini').addEventListener('click', (ev) => {
                ev.stopPropagation();
                const btn = ev.target.closest('.btn-mini-action');
                
                apiRequest('add_cart', { article: art }).then(res => {
                   if(res.status === 'success') {
                       updateCartUI(res.cart);
                       btn.style.color = '#28a745';
                   }
                });
            });

            actionsDiv.querySelector('.btn-fav-mini').addEventListener('click', (ev) => {
                ev.stopPropagation();
                const btn = ev.target.closest('.btn-mini-action');

                apiRequest('add_fav', { article: art }).then(res => {
                   if(res.status === 'success') {
                       btn.style.fill = '#ff3333';
                       btn.style.color = '#ff3333';
                   } else {
                       if(res.message) alert(res.message);
                   }
                });
            });
        });
    }
});