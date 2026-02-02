document.addEventListener('DOMContentLoaded', () => {
    
    // --- ПЕРЕКЛЮЧАТЕЛЬ ЮР/ФИЗ ЛИЦО ---
    const radios = document.querySelectorAll('input[name="entity_type"]');
    const legalBlock = document.getElementById('legal-block');
    const legalInputs = legalBlock.querySelectorAll('input');

    radios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.value === 'yur') {
                legalBlock.style.display = 'block';
                legalInputs.forEach(inp => inp.required = true);
            } else {
                legalBlock.style.display = 'none';
                legalInputs.forEach(inp => inp.required = false);
            }
            recalcHiddenMessage(); 
        });
    });

    // --- ОБРАБОТКА ФОРМЫ ---
    const form = document.getElementById('checkoutForm');
    if(form) {
        form.addEventListener('input', recalcHiddenMessage);
        form.addEventListener('change', recalcHiddenMessage);
        
        form.onsubmit = async (e) => {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = 'ОБРАБОТКА...';
            btn.style.opacity = '0.7';
            btn.disabled = true;

            const fd = new FormData(form);

            try {
                // 1. СНАЧАЛА СОХРАНЯЕМ В БАЗУ (ГЛАВНЫЙ ПРИОРИТЕТ)
                fd.append('action', 'save_order');
                const dbResponse = await fetch('api_actions.php', { method: 'POST', body: fd });
                
                // Проверяем ответ сервера
                const dbText = await dbResponse.text();
                let dbResult;
                try {
                    dbResult = JSON.parse(dbText);
                } catch (e) {
                    console.error('Ответ сервера (не JSON):', dbText);
                    throw new Error('Ошибка сервера БД. Свяжитесь с поддержкой.');
                }

                if (dbResult.status !== 'success') {
                    throw new Error('Ошибка сохранения заказа: ' + dbResult.message);
                }

                // 2. ЕСЛИ СОХРАНИЛОСЬ -> ОТПРАВЛЯЕМ В TELEGRAM
                // Добавляем номер заказа в начало сообщения для Телеграма
                let currentMsg = fd.get('message') || '';
                fd.set('message', `ЗАКАЗ №${dbResult.order_id}\n` + currentMsg);
                
                // Отправляем в фоне (не ждем ответа, чтобы не задерживать клиента)
                // Если Телеграм ляжет, клиент все равно увидит успешный экран
                fetch('send.php', { method: 'POST', body: fd }).catch(err => console.error('TG Error:', err));

                // 3. ПОКАЗЫВАЕМ ЭКРАН УСПЕХА
                const grid = document.querySelector('.checkout-grid');
                grid.innerHTML = `
                    <div class="tech-card" style="grid-column: 1 / -1; text-align:center; padding: 60px 20px;">
                        <div style="width:80px; height:80px; margin:0 auto 20px auto; color:#fff;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <h2 style="color: #fff; margin-bottom: 15px; font-family:var(--font-head); text-transform:uppercase;">ЗАКАЗ №${dbResult.order_id} ПРИНЯТ!</h2>
                        <p style="color:#888; max-width: 500px; margin: 0 auto 30px;">
                            Спасибо, ${fd.get('name')}! <br>
                            Мы получили вашу заявку и сохранили её в истории заказов.
                        </p>
                        <a href="profile.php" class="btn btn-main" style="margin-right: 10px;">В ЛИЧНЫЙ КАБИНЕТ</a>
                        <a href="catalog.php" class="btn btn-main" style="background:transparent; border:1px solid #555;">В КАТАЛОГ</a>
                    </div>
                `;
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                // Обнуляем корзину в шапке
                if(typeof updateCartUI === 'function') updateCartUI([]);

            } catch (error) {
                console.error(error);
                alert(error.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        };
    }
    
    recalcHiddenMessage();
});

// --- ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ---

function recalcHiddenMessage() {
    const hiddenMsg = document.getElementById('full-message');
    if(!hiddenMsg) return;

    const form = document.getElementById('checkoutForm');
    const fd = new FormData(form);
    
    const isYur = fd.get('entity_type') === 'yur';
    const company = fd.get('company_name');
    const inn = fd.get('inn');
    const address = fd.get('address');
    const comment = fd.get('comment');

    let txt = "------------------------\n";
    txt += `ТИП: ${isYur ? 'ЮР. ЛИЦО' : 'ФИЗ. ЛИЦО'}\n`;
    
    if(isYur) {
        if(company) txt += `КОМПАНИЯ: ${company}\n`;
        if(inn)     txt += `ИНН: ${inn}\n`;
    }
    
    // Имя и телефон уже есть в полях send.php, дублируем для наглядности в теле письма
    if(address) txt += `АДРЕС: ${address}\n`;
    if(comment) txt += `КОММЕНТ: ${comment}\n`;
    
    txt += "\nСОСТАВ ЗАКАЗА:\n";
    
    document.querySelectorAll('.summary-item').forEach(row => {
        const art = row.querySelector('.s-art').innerText;
        const name = row.querySelector('.s-name').innerText;
        const qty = row.querySelector('.qty-input-mini').value;
        txt += `• ${art} - ${name} (x${qty})\n`;
    });

    hiddenMsg.value = txt;
}

async function updateQty(btn, delta) {
    const row = btn.closest('.summary-item');
    const input = row.querySelector('.qty-input-mini');
    const partNumber = input.dataset.part;
    
    let currentQty = parseInt(input.value);
    if(currentQty + delta < 1) return; 

    input.value = currentQty + delta;
    updatePageTotals();
    recalcHiddenMessage();

    let fd = new FormData();
    fd.append('action', 'update_qty');
    fd.append('article', partNumber);
    fd.append('direction', delta > 0 ? 'plus' : 'minus');

    try {
        await fetch('api_actions.php', { method: 'POST', body: fd });
        if(typeof updateCartUI === 'function') {
            // Обновляем шапку без перезагрузки
            let res = await fetch('api_actions.php', { method: 'POST', body: new URLSearchParams({action: 'get_cart'})});
            let data = await res.json();
            updateCartUI(data.cart);
        }
    } catch(err) { console.error(err); }
}

async function removeItem(btn) {
    if(!confirm('Удалить позицию?')) return;

    const row = btn.closest('.summary-item');
    const input = row.querySelector('.qty-input-mini');
    const partNumber = input.dataset.part;

    row.style.opacity = '0';
    
    let fd = new FormData();
    fd.append('action', 'delete_item');
    fd.append('article', partNumber);

    try {
        await fetch('api_actions.php', { method: 'POST', body: fd });
        
        row.remove();
        updatePageTotals();
        recalcHiddenMessage();

        if(document.querySelectorAll('.summary-item').length === 0) {
            window.location.href = 'catalog.php';
        }

        if(typeof updateCartUI === 'function') {
            let res = await fetch('api_actions.php', { method: 'POST', body: new URLSearchParams({action: 'get_cart'})});
            let data = await res.json();
            updateCartUI(data.cart);
        }

    } catch(err) { 
        console.error(err); 
        row.style.opacity = '1';
    }
}

function updatePageTotals() {
    let totalQty = 0;
    let itemsCount = 0;
    
    document.querySelectorAll('.summary-item').forEach(row => {
        totalQty += parseInt(row.querySelector('.qty-input-mini').value);
        itemsCount++;
    });

    const countEl = document.getElementById('itemsCount');
    const totalEl = document.getElementById('totalQtyVal');

    if(countEl) countEl.innerText = itemsCount;
    if(totalEl) totalEl.innerText = totalQty + ' шт.';
}