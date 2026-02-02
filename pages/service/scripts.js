document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. АНИМАЦИЯ ПОЯВЛЕНИЯ ПРИ СКРОЛЛЕ ---
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-visible');
            }
        });
    }, { threshold: 0.1 });

    const animTargets = document.querySelectorAll('.tech-card, .price-table tr, .step-item, .faq-item');
    animTargets.forEach(el => {
        el.classList.add('animate-hidden');
        observer.observe(el);
    });

    // --- 2. МАСКА ТЕЛЕФОНА ---
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

    // --- 3. АККОРДЕОН (FAQ) ---
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
        const trigger = item.querySelector('.faq-head') || item;
        
        trigger.addEventListener('click', (e) => {
            item.classList.toggle('active');
            const body = item.querySelector('.faq-body');
            
            if (body) {
                if (item.classList.contains('active')) {
                    body.style.maxHeight = body.scrollHeight + "px";
                } else {
                    body.style.maxHeight = null;
                }
            }
        });
    });

    // --- 4. ОТПРАВКА ФОРМЫ (Telegram) ---
    // Ищем форму по ID, который мы добавили в HTML
    const form = document.getElementById('serviceForm') || document.querySelector('.service-form');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = form.querySelector('button[type="submit"]');
            // Сохраняем оригинальный текст кнопки (чтобы вернуть спаны после отправки)
            const originalBtnContent = btn.innerHTML; 
            
            // 1. Собираем данные из полей
            const formData = new FormData(this);
            
            // 2. Формируем сообщение для Telegram
            let fullMessage = "";
            
            const model = formData.get('model') ? formData.get('model').trim() : '';
            const location = formData.get('location') ? formData.get('location').trim() : '';
            const userMsg = formData.get('message') ? formData.get('message').trim() : '';
            
            if(model) fullMessage += `🚜 <b>Модель:</b> ${model}\n`;
            if(location) fullMessage += `📍 <b>Место:</b> ${location}\n`;
            if(userMsg) fullMessage += `📝 <b>Описание:</b> ${userMsg}`;
            
            // Подменяем поле message на наш собранный текст
            formData.set('message', fullMessage);

            // 3. Визуализация загрузки
            btn.disabled = true;
            btn.innerHTML = 'ОТПРАВКА...';

            // 4. Отправка на send.php
            fetch('send.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('✅ Ваша заявка принята! Инженер свяжется с вами в ближайшее время.');
                    form.reset(); // Очистить форму
                } else {
                    alert('❌ Ошибка отправки: ' + (data.message || 'Попробуйте позже'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Произошла ошибка сети. Попробуйте еще раз.');
            })
            .finally(() => {
                // Возвращаем кнопку в исходное состояние (с анимацией)
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;
            });
        });
    }
});