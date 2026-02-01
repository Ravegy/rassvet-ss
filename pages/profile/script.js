// pages/profile/script.js

// Раскрытие/скрытие заказа
function toggleOrder(element) {
    element.classList.toggle('active');
}

// [НОВОЕ] Смена статуса (для Админа)
async function changeStatus(selectElement, orderId) {
    const newStatus = selectElement.value;
    
    // Меняем цвет селекта сразу для красоты
    selectElement.className = 'status-select st-' + newStatus;

    // Отключаем на время запроса
    selectElement.disabled = true;

    try {
        let fd = new FormData();
        fd.append('action', 'update_order_status');
        fd.append('order_id', orderId);
        fd.append('status', newStatus);

        let response = await fetch('api_actions.php', { method: 'POST', body: fd });
        let result = await response.json();

        if (result.status === 'success') {
            // Успех, просто включаем обратно
            selectElement.disabled = false;
        } else {
            alert('Ошибка обновления: ' + result.message);
            selectElement.disabled = false;
        }
    } catch (err) {
        console.error(err);
        alert('Ошибка соединения');
        selectElement.disabled = false;
    }
}

// Стандартные скрипты
document.addEventListener('DOMContentLoaded', () => {
    // Маска телефона
    const phoneInput = document.querySelector('input[name="phone"]');
    if(phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
            if (!x[2] && x[1] !== '') {
                e.target.value = x[1] === '7' ? '+7 ' : '+7 ' + x[1];
            } else {
                e.target.value = !x[2] ? x[1] : '+7 (' + x[2] + (x[3] ? ') ' + x[3] : '') + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '');
            }
        });
    }

    // Сохранение профиля
    const form = document.getElementById('profileForm');
    if(form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = form.querySelector('.btn-save');
            const originalText = btn.innerText;
            btn.innerText = 'СОХРАНЕНИЕ...';
            btn.disabled = true;
            const fd = new FormData(form);
            try {
                let response = await fetch('api_actions.php', { method: 'POST', body: fd });
                let result = await response.json();
                if(result.status === 'success') {
                    btn.innerText = 'СОХРАНЕНО!';
                    btn.style.background = '#4CAF50';
                    btn.style.color = '#fff';
                    setTimeout(() => {
                        btn.innerText = originalText;
                        btn.style.background = ''; btn.style.color = ''; btn.disabled = false;
                    }, 2000);
                } else {
                    alert('Ошибка: ' + result.message);
                    btn.innerText = originalText; btn.disabled = false;
                }
            } catch (err) { console.error(err); btn.innerText = originalText; btn.disabled = false; }
        });
    }
});