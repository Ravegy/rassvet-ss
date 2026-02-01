<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Требуется авторизация | РАССВЕТ-С</title>
        <link rel="stylesheet" href="common.css?v=<?= time() ?>">
        <link rel="stylesheet" href="pages/checkout/style.css?v=<?= time() ?>">
    </head>
    <body>
        <?php include 'includes/header.php'; ?>

        <main class="checkout-page">
            <div class="container">
                <div class="auth-warning-wrapper">
                    <div class="tech-card auth-card">
                        <div class="auth-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        
                        <h1 class="auth-title">ТРЕБУЕТСЯ АВТОРИЗАЦИЯ</h1>
                        
                        <p class="auth-desc">
                            Для оформления заявки и выставления счета необходимо войти в личный кабинет.
                            Это делается в целях безопасности и позволяет нам:
                        </p>

                        <div class="auth-benefits">
                            <div class="benefit-item">
                                <span class="b-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                                <span>Автоматически заполнять ваши реквизиты</span>
                            </div>
                            <div class="benefit-item">
                                <span class="b-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                                <span>Сохранять историю ваших заказов</span>
                            </div>
                            <div class="benefit-item">
                                <span class="b-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
                                <span>Предоставлять персональные скидки</span>
                            </div>
                        </div>

                        <a href="login.php" class="btn btn-main btn-auth-go">ВОЙТИ ИЛИ СОЗДАТЬ АККАУНТ</a>
                    </div>
                </div>
            </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// [ИЗМЕНЕНИЕ] Подгружаем ВСЕ данные из профиля
$prefill_name    = htmlspecialchars($user['name'] ?? '');
$prefill_email   = htmlspecialchars($user['email'] ?? '');
$prefill_phone   = htmlspecialchars($user['phone'] ?? '');
$prefill_address = htmlspecialchars($user['address'] ?? $user['city'] ?? '');
$prefill_company = htmlspecialchars($user['company_name'] ?? '');
$prefill_inn     = htmlspecialchars($user['inn'] ?? '');

// Если заполнены данные юр. лица, сразу включаем переключатель "Юр. лицо"
$is_yur = (!empty($prefill_company) || !empty($prefill_inn));

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
    <link rel="stylesheet" href="pages/checkout/style.css?v=<?= time() ?>">
    <style>
        .legal-fields { display: <?= $is_yur ? 'block' : 'none' ?>; }
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
                
                <form id="checkoutForm" class="static-form">
                    
                    <div class="entity-switch">
                        <input type="radio" name="entity_type" id="type-fiz" value="fiz" class="switch-radio" <?= !$is_yur ? 'checked' : '' ?>>
                        <label for="type-fiz" class="switch-label">ФИЗИЧЕСКОЕ ЛИЦО</label>
                        
                        <input type="radio" name="entity_type" id="type-yur" value="yur" class="switch-radio" <?= $is_yur ? 'checked' : '' ?>>
                        <label for="type-yur" class="switch-label">ЮРИДИЧЕСКОЕ ЛИЦО</label>
                    </div>

                    <div class="form-group">
                        <label class="input-label">Контактное лицо</label>
                        <input type="text" name="name" class="c-input" value="<?= $prefill_name ?>" required>
                    </div>

                    <div id="legal-block" class="legal-fields">
                        <div class="form-group">
                            <label class="input-label">Название компании</label>
                            <input type="text" name="company_name" class="c-input" placeholder="Например: ООО «ЛесТранс»" value="<?= $prefill_company ?>">
                        </div>
                        <div class="form-group">
                            <label class="input-label">ИНН</label>
                            <input type="text" name="inn" class="c-input" placeholder="10 или 12 цифр" value="<?= $prefill_inn ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="input-label">Телефон</label>
                        <input type="tel" name="phone" class="c-input" placeholder="+7 (___) ___-__-__" required value="<?= $prefill_phone ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="input-label">Email (для ответа/счета)</label>
                        <input type="email" name="email" class="c-input" value="<?= $prefill_email ?>">
                    </div>

                    <div class="form-group">
                        <label class="input-label">Адрес доставки</label>
                        <input type="text" name="address" class="c-input" placeholder="Город, Улица, Дом..." value="<?= $prefill_address ?>">
                    </div>

                    <div class="form-group">
                        <label class="input-label">Комментарий к заказу</label>
                        <textarea name="comment" class="c-input c-area" placeholder="Желаемая ТК или вопросы..."></textarea>
                    </div>

                    <textarea name="message" id="full-message" style="display:none;"></textarea>

                    <button type="submit" class="btn btn-main btn-order">ПОДТВЕРДИТЬ ЗАКАЗ</button>
                    
                    <div style="text-align:center; margin-top:15px; font-size:12px; color:#555;">
                        Нажимая кнопку, вы соглашаетесь с <a href="policy.php" style="color:#777; text-decoration:underline;">политикой конфиденциальности</a>.
                    </div>
                </form>
            </div>

            <div class="tech-card order-summary">
                <span class="card-label">ВАШ ЗАКАЗ</span>
                
                <div class="summary-list">
                    <?php 
                    $totalQty = 0;
                    foreach ($cartItems as $item): 
                        $itemName = $item['name'] ?: 'Запчасть';
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
                
                <div class="summary-total">
                    Позиций: <span id="itemsCount"><?= count($cartItems) ?></span> <br>
                    Всего товаров: <span id="totalQtyVal"><?= $totalQty ?> шт.</span>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="pages/checkout/script.js?v=<?= time() ?>"></script>

</body>
</html>