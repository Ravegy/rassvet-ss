<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($action === 'register') {
        $name = trim($_POST['name']);
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Этот Email уже занят!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
            if ($stmt->execute([$email, $hash, $name])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                header('Location: profile.php');
                exit;
            } else {
                $error = "Ошибка регистрации.";
            }
        }
    } elseif ($action === 'login') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: profile.php');
            exit;
        } else {
            $error = "Неверный логин или пароль.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход | РАССВЕТ-С</title>
    <link rel="stylesheet" href="common.css?v=<?= time() ?>">
    <style>
        .auth-page { padding-top: 150px; padding-bottom: 100px; min-height: 80vh; }
        .auth-container { max-width: 400px; margin: 0 auto; background: #181818; padding: 40px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); }
        .auth-title { font-family: var(--font-head); font-size: 24px; color: #fff; text-align: center; margin-bottom: 30px; text-transform: uppercase; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; color: #888; font-size: 12px; margin-bottom: 5px; }
        .form-input { width: 100%; background: #222; border: 1px solid #333; color: #fff; padding: 12px; border-radius: 4px; font-size: 14px; }
        .form-input:focus { border-color: var(--yellow); outline: none; }
        .btn-auth { width: 100%; background: var(--yellow); color: #000; font-weight: 700; border: none; padding: 12px; cursor: pointer; border-radius: 4px; font-family: var(--font-head); text-transform: uppercase; margin-top: 10px; }
        .btn-auth:hover { background: #fff; }
        .auth-switch { text-align: center; margin-top: 20px; font-size: 13px; color: #666; cursor: pointer; }
        .auth-switch span { color: var(--yellow); text-decoration: underline; }
        .error-msg { background: rgba(255, 50, 50, 0.1); color: #ff3333; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center; font-size: 13px; }
        .tg-auth-block { margin-top: 30px; border-top: 1px solid #333; padding-top: 20px; text-align: center; }
        .tg-label { color: #888; font-size: 12px; margin-bottom: 10px; display: block; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<main class="auth-page">
    <div class="container">
        <div class="auth-container">
            <h1 class="auth-title" id="form-title">ВХОД</h1>

            <?php if ($error): ?>
                <div class="error-msg"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" id="auth-form" class="static-form">
                <input type="hidden" name="action" id="action-input" value="login">
                
                <div class="form-group" id="name-group" style="display:none;">
                    <label>Ваше имя</label>
                    <input type="text" name="name" class="form-input" placeholder="Иван">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-input" placeholder="mail@example.com" required>
                </div>

                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" class="form-input" placeholder="******" required>
                </div>

                <button type="submit" class="btn-auth" id="btn-submit">ВОЙТИ</button>
            </form>

            <div class="auth-switch" onclick="toggleAuth()">
                <span id="switch-text">Нет аккаунта? Зарегистрироваться</span>
            </div>

            <div class="tg-auth-block">
                <span class="tg-label">ИЛИ ЧЕРЕЗ TELEGRAM</span>
                <script async src="https://telegram.org/js/telegram-widget.js?22" 
                        data-telegram-login="rassvet_s_bot" 
                        data-size="large" 
                        data-radius="4" 
                        data-auth-url="tg_auth.php" 
                        data-request-access="write"></script>
            </div>
        </div>
    </div>
</main>

<script>
    let isLogin = true;
    function toggleAuth() {
        isLogin = !isLogin;
        const title = document.getElementById('form-title');
        const action = document.getElementById('action-input');
        const nameGroup = document.getElementById('name-group');
        const btn = document.getElementById('btn-submit');
        const switchText = document.getElementById('switch-text');
        const nameInput = document.querySelector('input[name="name"]');

        if (isLogin) {
            title.innerText = 'ВХОД';
            action.value = 'login';
            nameGroup.style.display = 'none';
            nameInput.required = false;
            btn.innerText = 'ВОЙТИ';
            switchText.innerText = 'Нет аккаунта? Зарегистрироваться';
        } else {
            title.innerText = 'РЕГИСТРАЦИЯ';
            action.value = 'register';
            nameGroup.style.display = 'block';
            nameInput.required = true;
            btn.innerText = 'СОЗДАТЬ АККАУНТ';
            switchText.innerText = 'Есть аккаунт? Войти';
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>