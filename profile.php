<?php
session_start();
require_once 'includes/db.php';

// Если не авторизован - на выход
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ОБРАБОТКА СОХРАНЕНИЯ ГОРОДА
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['city'])) {
    $city = trim($_POST['city']);
    $stmt = $pdo->prepare("UPDATE users SET city = ? WHERE id = ?");
    $stmt->execute([$city, $_SESSION['user_id']]);
    
    // Перезагружаем страницу, чтобы обновить данные
    header('Location: profile.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Выход
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
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
            <h1 class="page-title">ЛИЧНЫЙ КАБИНЕТ</h1>
            <div class="page-status"><span class="status-dot"></span> ОНЛАЙН</div>
        </div>
    </div>

    <div class="container container-sm">
        <div class="profile-card fade-up">
            <div class="profile-avatar">
                <?= mb_substr($user['name'], 0, 1) ?>
            </div>
            
            <div class="profile-info">
                <div class="profile-name"><?= htmlspecialchars($user['name']) ?></div>
                
                <div class="data-group">
                    <span class="data-label">Электронная почта</span>
                    <div class="data-value">
                        <?= $user['email'] ? htmlspecialchars($user['email']) : '<span style="color:#666">Не указана (вход через Telegram)</span>' ?>
                    </div>
                </div>

                <div class="data-group">
                    <span class="data-label">Ваш Город (Для доставки)</span>
                    <form method="POST" class="city-form static-form" id="cityForm">
                        <div class="input-with-btn">
                            <input type="text" name="city" id="userCity" 
                                   class="profile-input" 
                                   placeholder="Определяем..." 
                                   value="<?= htmlspecialchars($user['city']) ?>" 
                                   autocomplete="off">
                            <button type="submit" class="btn-save-mini" title="Сохранить">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="data-group">
                    <span class="data-label">Привязка Telegram</span>
                    <div>
                        <?php if ($user['telegram_id']): ?>
                            <div class="tg-status tg-linked">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                Подключен
                            </div>
                            <?php if($user['telegram_username']): ?>
                                <div style="font-size:12px; color:#666; margin-top:4px;">@<?= htmlspecialchars($user['telegram_username']) ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="tg-status tg-unlinked">Не подключен</div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!$user['telegram_id']): ?>
                    <div class="tg-connect-box">
                        <span class="tg-hint">Привяжите Telegram, чтобы входить на сайт в один клик без пароля:</span>
                        
                        <script async src="https://telegram.org/js/telegram-widget.js?22" 
                                data-telegram-login="rassvet_s_bot" 
                                data-size="medium" 
                                data-radius="4" 
                                data-auth-url="tg_auth.php" 
                                data-request-access="write"></script>
                    </div>
                <?php endif; ?>

                <a href="profile.php?logout=1" class="btn-logout">ВЫЙТИ ИЗ АККАУНТА</a>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Анимация появления
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('visible');
        });
    });
    document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));

    // ЛОГИКА АВТООПРЕДЕЛЕНИЯ ГОРОДА (ИСПРАВЛЕНО НА HTTPS)
    const cityInput = document.getElementById('userCity');
    
    // Если поле пустое - определяем IP через Sypex Geo (поддерживает HTTPS)
    if (cityInput && cityInput.value.trim() === '') {
        fetch('https://api.sypexgeo.net/json/')
            .then(response => response.json())
            .then(data => {
                // Sypex Geo возвращает объект city с полем name_ru
                if (data && data.city && data.city.name_ru) {
                    cityInput.value = data.city.name_ru;
                }
            })
            .catch(err => {
                console.log('Ошибка определения города:', err);
                cityInput.placeholder = 'Введите город вручную';
            });
    }
});
</script>

</body>
</html>