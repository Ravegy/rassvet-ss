<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$error = '';
$actionType = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $actionType = $action;

    if ($action === 'register') {
        $name = trim($_POST['name'] ?? '');
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email занят!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
            if ($stmt->execute([$email, $hash, $name])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                header('Location: profile.php');
                exit;
            } else {
                $error = "Ошибка регистрации";
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
            $error = "Неверные данные";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Вход | РАССВЕТ-С</title>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.9/css/unicons.css">
    
    <link rel="stylesheet" href="common.css?v=<?= time() ?>">
    <link rel="stylesheet" href="pages/login/style.css?v=<?= time() ?>">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="auth-wrap">
    <div class="container">
        
        <div class="page-header">
            <h1 class="page-title">ЛИЧНЫЙ КАБИНЕТ</h1>
            <div class="page-status"><span class="status-dot"></span> ДОСТУП В СИСТЕМУ</div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 text-center align-self-center">
                <div class="section text-center">
                    
                    <input class="checkbox" type="checkbox" id="reg-log" name="reg-log" 
                        <?= ($actionType === 'register' && $error) ? 'checked' : '' ?> 
                    />
                    
                    <label for="reg-log" class="toggle-btn"></label>
                    
                    <div class="card-3d-wrap mx-auto">
                        <div class="card-3d-wrapper">
                            
                            <div class="card-front">
                                <div class="center-wrap">
                                    <div class="section text-center">
                                        
                                        <h4 class="mb-4 pb-3">ВХОД</h4>
                                        
                                        <?php if ($error && ($actionType === 'login' || $actionType === '')): ?>
                                            <div class="error-msg"><?= $error ?></div>
                                        <?php endif; ?>

                                        <form action="login.php" method="POST" class="static-form">
                                            <input type="hidden" name="action" value="login">
                                            
                                            <div class="form-group">
                                                <input type="email" name="email" class="form-style" placeholder="Ваша почта" 
                                                       required autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                                                <i class="input-icon uil uil-at"></i>
                                            </div>  
                                            <div class="form-group mt-2">
                                                <input type="password" name="password" class="form-style" placeholder="Ваш пароль" 
                                                       required autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                                                <i class="input-icon uil uil-lock-alt"></i>
                                            </div>
                                            
                                            <?php 
                                                $isLoginErr = ($error && $actionType === 'login');
                                                $btnClass = $isLoginErr ? "btn-flip btn-error" : "btn-flip";
                                                $btnText = $isLoginErr ? "ОШИБКА" : "ВОЙТИ";
                                            ?>
                                            <button type="submit" class="<?= $btnClass ?>" id="login-btn" data-original="ВОЙТИ"><?= $btnText ?></button>
                                        </form>
                                        
                                        <a href="#" class="link">Забыли пароль?</a>

                                        <div class="tg-custom-wrapper">
                                            <div class="tg-visual-btn">
                                                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.48-.94-2.4-1.54-1.06-.7-.37-1.09.23-1.72.16-.16 2.92-2.67 2.97-2.9.01-.03.01-.14-.05-.2-.06-.06-.16-.04-.23-.02-.1.02-1.66 1.06-4.69 3.11-.45.3-.85.45-1.21.44-.4-.01-1.16-.22-1.73-.41-.7-.23-1.26-.35-1.21-.73.03-.2.3-.4.82-.6 3.22-1.4 5.37-2.33 6.45-2.78 3.07-1.28 3.71-1.5 4.12-1.51.09 0 .29.02.42.12.11.09.14.21.15.29 0 .09.01.25 0 .28z"/></svg>
                                                <span>Войти через Telegram</span>
                                            </div>
                                            <div class="tg-widget-overlay">
                                                <script async src="https://telegram.org/js/telegram-widget.js?22" 
                                                        data-telegram-login="rassvet_s_bot" 
                                                        data-size="large" 
                                                        data-radius="4" 
                                                        data-auth-url="tg_auth.php" 
                                                        data-request-access="write"></script>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="card-back">
                                <div class="center-wrap">
                                    <div class="section text-center">
                                        
                                        <h4 class="mb-4 pb-3">РЕГИСТРАЦИЯ</h4>
                                        
                                        <?php if ($error && $actionType === 'register'): ?>
                                            <div class="error-msg"><?= $error ?></div>
                                        <?php endif; ?>

                                        <form action="login.php" method="POST" class="static-form">
                                            <input type="hidden" name="action" value="register">

                                            <div class="form-group">
                                                <input type="text" name="name" class="form-style" placeholder="Ваше имя" 
                                                       required autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                                                <i class="input-icon uil uil-user"></i>
                                            </div>  
                                            <div class="form-group mt-2">
                                                <input type="email" name="email" class="form-style" placeholder="Ваша почта" 
                                                       required autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                                                <i class="input-icon uil uil-at"></i>
                                            </div>  
                                            <div class="form-group mt-2">
                                                <input type="password" name="password" class="form-style" placeholder="Придумайте пароль" 
                                                       required autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
                                                <i class="input-icon uil uil-lock-alt"></i>
                                            </div>
                                            
                                            <?php 
                                                $isRegErr = ($error && $actionType === 'register');
                                                $btnClass = $isRegErr ? "btn-flip btn-error" : "btn-flip";
                                                $btnText = $isRegErr ? "ОШИБКА" : "СОЗДАТЬ АККАУНТ";
                                            ?>
                                            <button type="submit" class="<?= $btnClass ?>" id="reg-btn" data-original="СОЗДАТЬ АККАУНТ"><?= $btnText ?></button>
                                        </form>

                                        <div class="tg-custom-wrapper">
                                            <div class="tg-visual-btn">
                                                <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.48-.94-2.4-1.54-1.06-.7-.37-1.09.23-1.72.16-.16 2.92-2.67 2.97-2.9.01-.03.01-.14-.05-.2-.06-.06-.16-.04-.23-.02-.1.02-1.66 1.06-4.69 3.11-.45.3-.85.45-1.21.44-.4-.01-1.16-.22-1.73-.41-.7-.23-1.26-.35-1.21-.73.03-.2.3-.4.82-.6 3.22-1.4 5.37-2.33 6.45-2.78 3.07-1.28 3.71-1.5 4.12-1.51.09 0 .29.02.42.12.11.09.14.21.15.29 0 .09.01.25 0 .28z"/></svg>
                                                <span>Войти через Telegram</span>
                                            </div>
                                            <div class="tg-widget-overlay">
                                                <script async src="https://telegram.org/js/telegram-widget.js?22" 
                                                        data-telegram-login="rassvet_s_bot" 
                                                        data-size="large" 
                                                        data-radius="4" 
                                                        data-auth-url="tg_auth.php" 
                                                        data-request-access="write"></script>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="pages/login/script.js?v=<?= time() ?>"></script>

<?php include 'includes/footer.php'; ?>
</body>
</html>