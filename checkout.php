<?php
// Файл: checkout.php
session_start();
require_once 'includes/db.php';

// 1. ПРОВЕРКА АВТОРИЗАЦИИ
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. ПОЛУЧАЕМ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$prefill_name = htmlspecialchars($user['name'] ?? '');
$prefill_email = htmlspecialchars($user['email'] ?? '');
$prefill_city = htmlspecialchars($user['city'] ?? ''); 

// 3. ПОЛУЧАЕМ КОРЗИНУ
$sql = "SELECT c.qty, c.part_number, 
        (SELECT name FROM parts WHERE part_number = c.part_number LIMIT 1) as name 
        FROM cart c 
        WHERE c.user_id = ?";
$stmtCart = $pdo->prepare($sql);
$stmtCart->execute([$user_id]);
$cartItems = $stmtCart->fetchAll();

if (empty($cartItems)) {
    header('Location: catalog.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа | РАССВЕТ-С</title>
    
    <link rel="stylesheet" href="common.css?v=<?= time() ?>">
    
    <style>
        /* --- СТИЛИ СТРАНИЦЫ (КАК В CONTACTS) --- */
        .checkout-page {
            padding-top: 140px;
            padding-bottom: 100px;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 0.8fr;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Карточки */
        .tech-card {
            background: rgba(30, 30, 30, 0.85);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            transition: 0.3s;
        }
        
        .tech-card:hover {
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .card-label {
            font-family: var(--font-head);
            font-size: 12px;
            font-weight: 700;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 10px;
            display: block;
        }

        /* Поля ввода */
        .c-input {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            font-family: var(--font-body);
            padding: 0 20px;
            height: 50px;
            border-radius: 4px;
            outline: none;
            transition: 0.3s;
            font-size: 14px;
            width: 100%;
            display: block;
            box-sizing: border-box;
        }
        .c-input:focus { border-color: var(--yellow); background: rgba(0, 0, 0, 0.3); }
        .c-area { height: 100px; padding-top: 15px; resize: vertical; }
        .form-group { margin-bottom: 20px; }
        
        .input-label {
            display: block;
            margin-bottom: 8px;
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            font-family: var(--font-head);
        }

        /* Переключатель */
        .entity-switch {
            display: flex;
            background: rgba(0,0,0,0.3);
            padding: 4px;
            border-radius: 6px;
            margin-bottom: 30px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .switch-radio { display: none; }
        .switch-label {
            flex: 1;
            text-align: center;
            padding: 12px;
            cursor: pointer;
            color: #888;
            font-weight: 600;
            font-size: 13px;
            transition: 0.3s;
            border-radius: 4px;
            font-family: var(--font-head);
            text-transform: uppercase;
        }
        .switch-radio:checked + .switch-label {
            background: rgba(255, 51, 51, 0.15);
            color: #fff;
            border: 1px solid rgba(255, 51, 51, 0.3);
        }

        .legal-fields { display: none; animation: fadeIn 0.4s ease forwards; }

        /* ПРАВАЯ КОЛОНКА */
        .order-summary { position: sticky; top: 100px; }
        
        .summary-list {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
            padding-right: 5px;
        }
        .summary-list::-webkit-scrollbar { width: 4px; }
        .summary-list::-webkit-scrollbar-track { background: #111; }
        .summary-list::-webkit-scrollbar-thumb { background: var(--yellow); border-radius: 2px; }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            transition: 0.2s;
        }
        
        .item-info { flex: 1; padding-right: 15px; }
        .s-art { color: var(--yellow); font-weight: 700; font-size: 13px; font-family: var(--font-head); display: block; }
        .s-name { color: #ccc; font-size: 12px; display: block; margin-top: 3px; line-height: 1.3; }
        
        /* Управление количеством (мини) */
        .qty-controls {
            display: flex;
            align-items: center;
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 4px;
            padding: 2px;
        }
        
        .btn-qty-mini {
            width: 24px;
            height: 24px;
            background: transparent;
            border: none;
            color: #888;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }
        .btn-qty-mini:hover { color: #fff; background: rgba(255,255,255,0.1); border-radius: 2px; }
        
        .qty-input-mini {
            width: 30px;
            background: transparent;
            border: none;
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            text-align: center;
            padding: 0;
        }
        
        .btn-del-mini {
            margin-left: 10px;
            background: transparent;
            border: none;
            color: #555;
            cursor: pointer;
            transition: 0.2s;
            padding: 5px;
            display: flex;
        }
        .btn-del-mini:hover { color: #ff3333; transform: scale(1.1); }

        .summary-total {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 15px;
            text-align: right;
            font-size: 13px;
            color: #888;
        }
        .summary-total span { color: #fff; font-size: 18px; font-weight: 700; margin-left: 10px; }

        .btn-order {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 900px) {
            .checkout-grid { grid-template-columns: 1fr; }
            .order-summary { order: -1; position: relative; top: 0; margin-bottom: 30px; }
            .checkout-page { padding-top: 100px; }
        }
    </style>
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="checkout-page">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">ОФОРМЛЕНИЕ ЗАКАЗА</h1>
            <div class="page-status"><span class="status-dot"></span> ПРОВЕРКА ДАННЫХ</div>
        </div>

        <div class="checkout-grid">
            
            <div class="tech-card">
                <span class="card-label">КОНТАКТНЫЕ ДАННЫЕ</span>
                
                <form id="checkoutForm">
                    
                    <div class="entity-switch">
                        <input type="radio" name="entity_type" id="type-fiz" value="fiz" class="switch-radio" checked>
                        <label for="type-fiz" class="switch-label">ФИЗИЧЕСКОЕ ЛИЦО</label>
                        
                        <input type="radio" name="entity_type" id="type-yur" value="yur" class="switch-radio">
                        <label for="type-yur" class="switch-label">ЮРИДИЧЕСКОЕ ЛИЦО</label>
                    </div>

                    <div class="form-group">
                        <label class="input-label">Контактное лицо</label>
                        <input type="text" name="name" class="c-input" value="<?= $prefill_name ?>" required>
                    </div>

                    <div id="legal-block" class="legal-fields">
                        <div class="form-group">
                            <label class="input-label">Название компании</label>
                            <input type="text" name="company_name" class="c-input" placeholder="Например: ООО «ЛесТранс»">
                        </div>
                        <div class="form-group">
                            <label class="input-label">ИНН</label>
                            <input type="text" name="inn" class="c-input" placeholder="10 или 12 цифр">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">Телефон</label>
                        <input type="tel" name="phone" class="c-input" placeholder="+7 (___) ___-__-__" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="input-label">Email (для ответа/счета)</label>
                        <input type="email" name="email" class="c-input" value="<?= $prefill_email ?>">
                    </div>

                    <div class="form-group">
                        <label class="input-label">Адрес доставки</label>
                        <input type="text" name="address" class="c-input" placeholder="Город, Улица, Дом..." value="<?= $prefill_city ?>">
                    </div>

                    <div class="form-group">
                        <label class="input-label">Комментарий к заказу</label>
                        <textarea name="comment" class="c-input c-area" placeholder="Желаемая ТК или вопросы..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-main btn-order">ПОДТВЕРДИТЬ ЗАКАЗ</button>
                    
                    <div style="text-align:center; margin-top:15px; font-size:12px; color:#555;">
                        Нажимая кнопку, вы соглашаетесь с <a href="policy.php" style="color:#777; text-decoration:underline;">политикой конфиденциальности</a>.
                    </div>
                </form>
            </div>

            <div class="tech-card order-summary">
                <span class="card-label">ВАШ ЗАКАЗ</span>
                
                <div class="summary-list" id="summaryList">
                    <?php 
                    $orderTextList = ""; 
                    $totalQty = 0;
                    foreach ($cartItems as $item): 
                        $itemName = $item['name'] ?: 'Запчасть';
                        $orderTextList .= "• {$item['part_number']} - {$itemName} (x{$item['qty']})\n";
                        $totalQty += $item['qty'];
                    ?>
                        <div class="summary-item" id="row-<?= $item['part_number'] ?>">
                            <div class="item-info">
                                <span class="s-art"><?= $item['part_number'] ?></span>
                                <span class="s-name"><?= $itemName ?></span>
                            </div>
                            
                            <div class="qty-controls">
                                <button type="button" class="btn-qty-mini" onclick="updateQty(this, -1)">−</button>
                                <input type="text" class="qty-input-mini" value="<?= $item['qty'] ?>" readonly data-part="<?= $item['part_number'] ?>">
                                <button type="button" class="btn-qty-mini" onclick="updateQty(this, 1)">+</button>
                            </div>

                            <button type="button" class="btn-del-mini" onclick="removeItem(this)" title="Удалить">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="summary-total" id="summaryTotalBlock">
                    Позиций: <span id="itemsCount"><?= count($cartItems) ?></span> <br>
                    Всего товаров: <span id="totalQtyVal"><?= $totalQty ?> шт.</span>
                </div>

                <textarea id="order-content" style="display:none;"><?= $orderTextList ?></textarea>
            </div>

        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
// --- ФУНКЦИИ УПРАВЛЕНИЯ КОРЗИНОЙ ---

// 1. Изменение кол-ва
async function updateQty(btn, delta) {
    const row = btn.closest('.summary-item');
    const input = row.querySelector('.qty-input-mini');
    const partNumber = input.dataset.part;
    
    let currentQty = parseInt(input.value);
    
    // Блокируем уход в минус
    if(currentQty + delta < 1) return; 

    // Визуально меняем сразу
    input.value = currentQty + delta;
    recalcTotal();

    // Шлем на сервер правильные ключи ('article' и 'direction')
    let fd = new FormData();
    fd.append('action', 'update_qty');
    fd.append('article', partNumber);
    fd.append('direction', delta > 0 ? 'plus' : 'minus');

    try {
        let response = await fetch('api_actions.php', { method: 'POST', body: fd });
        let res = await response.json();
        
        // Обновляем шапку, если вернулся успех
        if(res.status === 'success' && typeof updateCartUI === 'function') {
            updateCartUI(res.cart);
        }
    } catch(err) {
        console.error('Sync error:', err);
    }
}

// 2. Удаление
async function removeItem(btn) {
    if(!confirm('Удалить позицию?')) return;

    const row = btn.closest('.summary-item');
    const input = row.querySelector('.qty-input-mini');
    const partNumber = input.dataset.part;

    row.style.opacity = '0';
    
    let fd = new FormData();
    fd.append('action', 'delete_item');
    fd.append('article', partNumber); // Тут ключ 'article'

    try {
        let response = await fetch('api_actions.php', { method: 'POST', body: fd });
        let res = await response.json();

        if(res.status === 'success') {
            row.remove();
            recalcTotal();
            
            if(document.querySelectorAll('.summary-item').length === 0) {
                window.location.href = 'catalog.php';
            }
            if(typeof updateCartUI === 'function') {
                updateCartUI(res.cart);
            }
        }
    } catch(err) {
        console.error('Delete error:', err);
        row.style.opacity = '1';
    }
}

// 3. Пересчет
function recalcTotal() {
    let totalQty = 0;
    let itemsCount = 0;
    let textList = "";
    
    document.querySelectorAll('.summary-item').forEach(row => {
        const name = row.querySelector('.s-name').innerText;
        const art = row.querySelector('.s-art').innerText;
        const qty = parseInt(row.querySelector('.qty-input-mini').value);
        
        totalQty += qty;
        itemsCount++;
        textList += `• ${art} - ${name} (x${qty})\n`;
    });

    document.getElementById('itemsCount').innerText = itemsCount;
    document.getElementById('totalQtyVal').innerText = totalQty + ' шт.';
    document.getElementById('order-content').value = textList;
}

// --- СТАНДАРТНАЯ ЛОГИКА ---
document.addEventListener('DOMContentLoaded', () => {
    
    // Переключатель ФИЗ/ЮР
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
        });
    });

    // Отправка формы
    const form = document.getElementById('checkoutForm');
    if(form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = 'ОБРАБОТКА...';
            btn.disabled = true;
            btn.style.opacity = '0.7';

            const fd = new FormData(form);
            const isYur = fd.get('entity_type') === 'yur';
            const orderContent = document.getElementById('order-content').value;
            
            let msg = "🚀 <b>НОВЫЙ ЗАКАЗ</b>\n";
            msg += "========================\n";
            msg += `📋 <b>Тип:</b> ${isYur ? 'ЮРИДИЧЕСКОЕ ЛИЦО' : 'ФИЗИЧЕСКОЕ ЛИЦО'}\n`;
            
            msg += `👤 <b>Контакт:</b> ${fd.get('name')}\n`;
            
            if(isYur) {
                msg += `🏢 <b>Компания:</b> ${fd.get('company_name')}\n`;
                msg += `📑 <b>ИНН:</b> ${fd.get('inn')}\n`;
            }
            
            msg += `📞 <b>Телефон:</b> ${fd.get('phone')}\n`;
            if(fd.get('email')) msg += `📧 <b>Email:</b> ${fd.get('email')}\n`;
            msg += `🚚 <b>Адрес:</b> ${fd.get('address')}\n`;
            
            if(fd.get('comment')) {
                msg += "------------------------\n";
                msg += `💬 <b>Комментарий:</b> ${fd.get('comment')}\n`;
            }
            
            msg += "========================\n";
            msg += "📦 <b>СОСТАВ ЗАКАЗА:</b>\n" + orderContent; 

            fd.set('message', msg);

            try {
                let response = await fetch('send.php', { method: 'POST', body: fd });
                let result = await response.json();

                if (result.status === 'success') {
                    const grid = document.querySelector('.checkout-grid');
                    grid.innerHTML = `
                        <div class="tech-card" style="grid-column: 1 / -1; text-align:center; padding: 60px 20px;">
                            <div style="font-size: 60px; margin-bottom: 20px;">✅</div>
                            <h2 style="color: #fff; margin-bottom: 15px; font-family:var(--font-head);">ЗАКАЗ ПРИНЯТ!</h2>
                            <p style="color:#888; max-width: 500px; margin: 0 auto 30px;">
                                Спасибо, ${fd.get('name')}! <br>
                                Мы получили вашу заявку. В ближайшее время менеджер проверит наличие и свяжется с вами.
                            </p>
                            <a href="catalog.php" class="btn btn-main">В КАТАЛОГ</a>
                        </div>
                    `;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    // Очищаем шапку (визуально)
                    if(typeof updateCartUI === 'function') updateCartUI([]);
                } else {
                    throw new Error(result.message);
                }
            } catch (err) {
                alert('Ошибка отправки: ' + err.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        });
    }

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
});
</script>
</body>
</html>