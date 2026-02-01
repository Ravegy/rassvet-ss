<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$is_admin = ($user['is_admin'] == 1);

if ($is_admin) {
    $sql = "SELECT o.*, u.name as reg_name, u.email as reg_email FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
    $stmtOrders = $pdo->prepare($sql);
    $stmtOrders->execute();
} else {
    $stmtOrders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmtOrders->execute([$user_id]);
}
$orders = $stmtOrders->fetchAll();

$stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
foreach ($orders as $k => $ord) {
    $stmtItems->execute([$ord['id']]);
    $orders[$k]['items'] = $stmtItems->fetchAll();
}

$statuses = [
    'new' => 'В ОБРАБОТКЕ', 'work' => 'В РАБОТЕ', 'shipped' => 'ОТГРУЖЕН', 'done' => 'ВЫПОЛНЕН', 'cancel' => 'ОТМЕНЕН'
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет | РАССВЕТ-С</title>
    <link rel="stylesheet" href="common.css?v=<?= time() ?>">
    <link rel="stylesheet" href="pages/profile/style.css?v=<?= time() ?>">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="profile-page">
    <div class="container">
        
        <div class="page-header">
            <h1 class="page-title">ЛИЧНЫЙ КАБИНЕТ <?= $is_admin ? '<span style="color:#ff3333; font-size:0.6em; vertical-align:middle;">(ADMIN)</span>' : '' ?></h1>
            <div class="page-status"><span class="status-dot"></span> УПРАВЛЕНИЕ АККАУНТОМ</div>
        </div>

        <div class="profile-grid">
            
            <div class="tech-card profile-info">
                <span class="card-label">МОИ ДАННЫЕ</span>
                <form class="static-form profile-form">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-group">
                        <label class="input-label">Имя</label>
                        <input type="text" name="name" class="c-input" value="<?= htmlspecialchars($user['name']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="input-label">Телефон</label>
                        <input type="tel" name="phone" class="c-input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+7...">
                    </div>
                    <div class="form-group">
                        <label class="input-label">Email</label>
                        <input type="email" name="email" class="c-input" value="<?= htmlspecialchars($user['email']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="input-label">Адрес доставки</label>
                        <input type="text" name="address" class="c-input" value="<?= htmlspecialchars($user['address'] ?? '') ?>" placeholder="Город, улица...">
                    </div>
                    
                    <button type="submit" class="btn-save-custom">СОХРАНИТЬ ИЗМЕНЕНИЯ</button>
                    <a href="logout.php" class="btn-logout">ВЫЙТИ ИЗ АККАУНТА</a>
                </form>
            </div>

            <div class="tech-card profile-org">
                <span class="card-label">РЕКВИЗИТЫ ОРГАНИЗАЦИИ</span>
                <form class="static-form profile-form">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-group">
                        <label class="input-label">Название компании</label>
                        <input type="text" name="company_name" class="c-input" value="<?= htmlspecialchars($user['company_name'] ?? '') ?>" placeholder='ООО "Пример"'>
                    </div>
                    <div class="form-group">
                        <label class="input-label">ИНН</label>
                        <input type="text" name="inn" class="c-input" value="<?= htmlspecialchars($user['inn'] ?? '') ?>" placeholder="Введите ИНН">
                    </div>
                    <button type="submit" class="btn-save-custom">СОХРАНИТЬ РЕКВИЗИТЫ</button>
                </form>
            </div>

            <div class="tech-card profile-tg">
                <span class="card-label">TELEGRAM</span>
                <div class="tg-status">
                    <?php if (!empty($user['telegram_id'])): ?>
                        <div class="tg-connected">
                            <div class="tg-icon-ok"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div>
                                <div class="tg-title">ПОДКЛЮЧЕНО</div>
                                <?php if($user['telegram_username']): ?><div class="tg-user">@<?= htmlspecialchars($user['telegram_username']) ?></div><?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="tg-disconnected">
                            <div class="tg-widget-wrap">
                                <script async src="https://telegram.org/js/telegram-widget.js?22" 
                                        data-telegram-login="rassvet_s_bot" 
                                        data-size="medium" 
                                        data-radius="4" 
                                        data-auth-url="tg_auth.php" 
                                        data-request-access="write"></script>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tech-card profile-history">
                <span class="card-label"><?= $is_admin ? 'ВСЕ ЗАКАЗЫ ПОЛЬЗОВАТЕЛЕЙ' : 'ИСТОРИЯ ЗАКАЗОВ' ?></span>
                
                <div class="orders-list">
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $ord): ?>
                            <div class="order-row <?= $is_admin ? 'admin-row' : '' ?>" onclick="toggleOrder(this)">
                                
                                <div class="ord-header">
                                    <div class="ord-left">
                                        <div class="ord-top-line">
                                            <span class="ord-id">#<?= $ord['id'] ?></span>
                                            <span class="ord-date"><?= date('d.m.y H:i', strtotime($ord['created_at'])) ?></span>
                                        </div>
                                        <?php if($is_admin): ?>
                                            <div class="ord-client">
                                                👤 <?= htmlspecialchars($ord['customer_name'] ?: $ord['reg_name']) ?> <br>
                                                📞 <?= htmlspecialchars($ord['customer_phone']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="ord-right" onclick="event.stopPropagation()">
                                        <?php if ($is_admin): ?>
                                            <select class="status-select st-<?= $ord['status'] ?>" onchange="changeStatus(this, <?= $ord['id'] ?>)">
                                                <?php foreach($statuses as $key => $label): ?>
                                                    <option value="<?= $key ?>" <?= $ord['status'] == $key ? 'selected' : '' ?>><?= $label ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php else: ?>
                                            <span class="ord-status st-<?= $ord['status'] ?>">
                                                <?= $statuses[$ord['status']] ?? 'НЕИЗВЕСТНО' ?>
                                            </span>
                                        <?php endif; ?>
                                        <span class="ord-arrow" onclick="this.closest('.order-row').click()">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="ord-details">
                                    Товаров: <b><?= $ord['total_qty'] ?> шт.</b> 
                                    <?php if($ord['company_name']): ?>
                                        <div class="ord-company">🏢 <?= htmlspecialchars($ord['company_name']) ?> (ИНН: <?= $ord['inn'] ?>)</div>
                                    <?php endif; ?>
                                    <?php if($ord['comment']): ?>
                                        <div class="ord-comment">💬 <?= htmlspecialchars($ord['comment']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="ord-products">
                                    <?php foreach ($ord['items'] as $item): ?>
                                        <div class="prod-item">
                                            <div class="prod-info">
                                                <span class="prod-art"><?= $item['part_number'] ?></span>
                                                <span class="prod-name"><?= $item['name'] ?></span>
                                            </div>
                                            <div class="prod-qty">x<?= $item['qty'] ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-history">Список заказов пуст.</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
<script src="pages/profile/script.js?v=<?= time() ?>"></script>

</body>
</html>