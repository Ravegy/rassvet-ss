document.addEventListener('DOMContentLoaded', () => {
    initPhoneMasks();
    initUniversalForm();
    initCartLogic();
    initUI();
});

// --- ФУНКЦИЯ УВЕДОМЛЕНИЙ (Вместо alert) ---
function showNotification(title, message, type = 'success') {
    // Удаляем старое уведомление, если есть
    const existing = document.querySelector('.custom-toast');
    if (existing) existing.remove();

    // HTML иконки (галочка или крестик)
    let iconSvg = '';
    if (type === 'success') {
        iconSvg = `<svg viewBox="0 0 512 512" fill="currentColor"><path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"></path></svg>`;
    } else {
        iconSvg = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`;
    }

    // Создаем элемент
    const toast = document.createElement('div');
    toast.className = `custom-toast ${type}`;
    toast.innerHTML = `
        <svg class="toast-wave" viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg"><path d="M0,256L11.4,240C22.9,224,46,192,69,192C91.4,192,114,224,137,234.7C160,245,183,235,206,213.3C228.6,192,251,160,274,149.3C297.1,139,320,149,343,181.3C365.7,213,389,267,411,282.7C434.3,299,457,277,480,250.7C502.9,224,526,192,549,181.3C571.4,171,594,181,617,208C640,235,663,277,686,256C708.6,235,731,149,754,192C777.1,235,800,405,823,416C845.7,427,869,277,891,250.7C914.3,224,937,320,960,320C982.9,320,1006,224,1029,213.3C1051.4,203,1074,277,1097,298.7C1120,320,1143,288,1166,256C1188.6,224,1211,192,1234,192C1257.1,192,1280,224,1303,245.3C1325.7,267,1349,277,1371,266.7C1394.3,256,1417,224,1429,208L1440,192L1440,320L1428.6,320C1417.1,320,1394,320,1371,320C1348.6,320,1326,320,1303,320C1280,320,1257,320,1234,320C1211.4,320,1189,320,1166,320C1142.9,320,1120,320,1097,320C1074.3,320,1051,320,1029,320C1005.7,320,983,320,960,320C937.1,320,914,320,891,320C868.6,320,846,320,823,320C800,320,777,320,754,320C731.4,320,709,320,686,320C662.9,320,640,320,617,320C594.3,320,571,320,549,320C525.7,320,503,320,480,320C457.1,320,434,320,411,320C388.6,320,366,320,343,320C320,320,297,320,274,320C251.4,320,229,320,206,320C182.9,320,160,320,137,320C114.3,320,91,320,69,320C45.7,320,23,320,11,320L0,320Z"></path></svg>
        <div class="toast-icon-box">${iconSvg}</div>
        <div class="toast-content">
            <span class="toast-title">${title}</span>
            <span class="toast-message">${message}</span>
        </div>
    `;

    document.body.appendChild(toast);

    // Анимация появления
    setTimeout(() => toast.classList.add('show'), 100);

    // Автоудаление через 4 секунды
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}

// Безопасная очистка текста (чтобы не сломать HTML)
function escapeHtml(text) {
    if (!text) return text;
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// --- 1. ЕДИНЫЙ ОТПРАВЩИК ФОРМ ---
function initUniversalForm() {
    const forms = document.querySelectorAll('.js-send-form');

    forms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = form.querySelector('button[type="submit"]');
            const originalBtnContent = btn.innerHTML; 

            // Собираем данные
            const formData = new FormData(this);

            let extraInfo = "";
            const model = formData.get('model');
            const vin = formData.get('vin');
            const location = formData.get('location');
            const part = formData.get('part');
            
            if (model) extraInfo += `🚜 <b>Модель техники:</b> ${escapeHtml(model)}\n`;
            if (vin) extraInfo += `🔢 <b>VIN/Артикул:</b> ${escapeHtml(vin)}\n`;
            if (part) extraInfo += `⚙️ <b>Запчасть:</b> ${escapeHtml(part)}\n`;
            if (location) extraInfo += `📍 <b>Местонахождение:</b> ${escapeHtml(location)}\n`;

            const userMsg = formData.get('message') || '';
            const finalMessage = extraInfo + (extraInfo && userMsg ? '\n' : '') + (userMsg ? `📝 <b>Сообщение:</b> ${escapeHtml(userMsg)}` : '');
            
            formData.set('message', finalMessage);

            // Визуализация загрузки
            btn.disabled = true;
            btn.classList.add('loading');
            if(btn.querySelector('span')) {
                btn.innerHTML = 'ОТПРАВКА...'; 
            } else {
                btn.textContent = 'ОТПРАВКА...';
            }

            try {
                const response = await fetch('send.php', { method: 'POST', body: formData });
                const data = await response.json();

                if (data.status === 'success') {
                    // !!! ЗДЕСЬ ТЕПЕРЬ КРАСИВОЕ УВЕДОМЛЕНИЕ !!!
                    showNotification('УСПЕШНО!', 'Ваша заявка отправлена.<br>Мы свяжемся с вами в ближайшее время.', 'success');
                    form.reset();
                } else {
                    showNotification('ОШИБКА', data.message || 'Не удалось отправить заявку.', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('ОШИБКА СЕТИ', 'Проверьте соединение и попробуйте позже.', 'error');
            } finally {
                btn.disabled = false;
                btn.classList.remove('loading');
                btn.innerHTML = originalBtnContent;
            }
        });
    });
}

// --- 2. МАСКА ТЕЛЕФОНА ---
function initPhoneMasks() {
    const phoneInputs = document.querySelectorAll('input[type="tel"], input[name="phone"]');
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
}

// --- 3. ИНТЕРФЕЙС (UI) ---
function initUI() {
    const burger = document.querySelector('.burger-btn');
    const nav = document.querySelector('.nav');
    if (burger && nav) {
        burger.addEventListener('click', (e) => {
            e.stopPropagation();
            burger.classList.toggle('active');
            nav.classList.toggle('active');
        });
        document.addEventListener('click', (e) => {
            if (!nav.contains(e.target) && !burger.contains(e.target)) {
                burger.classList.remove('active');
                nav.classList.remove('active');
            }
        });
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('animate-visible');
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.tech-card, .price-table tr, .step-item, .hero-card, .service-card').forEach(el => {
        el.classList.add('animate-hidden');
        observer.observe(el);
    });

    document.querySelectorAll('.faq-item').forEach(item => {
        item.addEventListener('click', () => item.classList.toggle('active'));
    });

    document.querySelectorAll('.copy-text').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const text = btn.getAttribute('data-copy');
            if (text) {
                navigator.clipboard.writeText(text);
                // Используем новое уведомление и здесь
                showNotification('СКОПИРОВАНО', 'Текст скопирован в буфер обмена.', 'success');
            }
        });
    });
}

// --- 4. ЛОГИКА КОРЗИНЫ ---
function initCartLogic() {
    const cartToggle = document.querySelector('.header-cart-btn') || document.getElementById('cart-toggle');
    if(cartToggle) {
        cartToggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            let overlay = document.querySelector('.cart-overlay');
            if(!overlay) { window.updateCartUI([]); overlay = document.querySelector('.cart-overlay'); }
            if(overlay) overlay.classList.add('active');
        });
    }

    apiRequest('get_cart').then(res => { if(res.status === 'success') window.updateCartUI(res.cart); });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-add-cart')) {
            const btn = e.target;
            const art = btn.getAttribute('data-art');
            const originalText = btn.innerText;
            btn.innerText = '...';
            apiRequest('add_cart', { article: art }).then(res => {
                if(res.status === 'success') {
                    window.updateCartUI(res.cart);
                    btn.innerText = 'OK';
                    btn.style.background = '#28a745';
                    // Тоже добавим уведомление
                    showNotification('В КОРЗИНЕ', `Товар ${art} добавлен.`, 'success');
                    setTimeout(() => { btn.innerText = originalText; btn.style.background = ''; }, 1000);
                }
            });
        }
        if (e.target.closest('.btn-plus')) {
            const btn = e.target.closest('.btn-plus');
            apiRequest('update_qty', { article: btn.getAttribute('data-art'), direction: 'plus' }).then(res => window.updateCartUI(res.cart));
        }
        if (e.target.closest('.btn-minus')) {
            const btn = e.target.closest('.btn-minus');
            apiRequest('update_qty', { article: btn.getAttribute('data-art'), direction: 'minus' }).then(res => window.updateCartUI(res.cart));
        }
        if (e.target.closest('.btn-del-item')) {
            const btn = e.target.closest('.btn-del-item');
            apiRequest('delete_item', { article: btn.getAttribute('data-art') }).then(res => window.updateCartUI(res.cart));
        }
    });
}

window.updateCartUI = function(cart) {
    let overlay = document.querySelector('.cart-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'cart-overlay';
        document.body.appendChild(overlay);
        overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.classList.remove('active'); });
    }

    let totalQty = 0;
    if (cart) cart.forEach(i => totalQty += parseInt(i.qty));
    
    const badges = document.querySelectorAll('.cart-badge');
    badges.forEach(b => {
        b.innerText = totalQty;
        b.style.display = totalQty > 0 ? 'flex' : 'none';
    });

    // ИСПРАВЛЕНИЕ: Добавлен class="btn-close-cart" для кнопки закрытия
    let html = `<div class="cart-modal"><div class="cart-header"><span class="cart-title">ВАШ ЗАКАЗ (${totalQty})</span><button id="cart-close" class="btn-close-cart">✕</button></div><div class="cart-body">`;
    
    if (!cart || cart.length === 0) {
        html += `<div class="empty-cart-msg">Корзина пуста</div>`;
    } else {
        cart.forEach(item => {
            html += `<div class="cart-item-row"><div class="c-info"><span class="c-art">${item.part_number}</span><span class="c-name">${item.name||''}</span></div><div class="c-controls"><button class="btn-qty btn-minus" data-art="${item.part_number}">−</button><span>${item.qty}</span><button class="btn-qty btn-plus" data-art="${item.part_number}">+</button></div><button class="btn-del-item" data-art="${item.part_number}">✕</button></div>`;
        });
    }
    html += `</div>`;
    if (cart && cart.length > 0) html += `<div class="cart-footer"><a href="checkout.php" class="btn-order-cart">Оформить заказ</a></div>`;
    html += `</div>`;
    
    overlay.innerHTML = html;
    overlay.querySelector('#cart-close').addEventListener('click', () => overlay.classList.remove('active'));
};

async function apiRequest(action, data = {}) {
    let formData = new FormData();
    formData.append('action', action);
    for(let key in data) formData.append(key, data[key]);
    try {
        let res = await fetch('api_actions.php', { method: 'POST', body: formData });
        return await res.json();
    } catch (e) { return { status: 'error' }; }
}