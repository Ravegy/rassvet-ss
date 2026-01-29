<?php
session_start();
require_once 'includes/db.php';

// Подключаем конфиг
$config = require 'config.php';
$bot_token = $config['tg_token'];

function checkTelegramAuthorization($auth_data) {
    global $bot_token;
    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);
    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
        $data_check_arr[] = $key . '=' . $value;
    }
    sort($data_check_arr);
    $data_check_string = implode("\n", $data_check_arr);
    $secret_key = hash('sha256', $bot_token, true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);
    if (strcmp($hash, $check_hash) !== 0) {
        throw new Exception('Data is NOT from Telegram');
    }
    if ((time() - $auth_data['auth_date']) > 86400) {
        throw new Exception('Data is outdated');
    }
    return $auth_data;
}

try {
    $data = checkTelegramAuthorization($_GET);
    
    $tg_id = $data['id'];
    $first_name = $data['first_name'] ?? 'User';
    $username = $data['username'] ?? '';

    // ========================================================
    // СЦЕНАРИЙ 1: ПРИВЯЗКА (Пользователь уже вошел через Email)
    // ========================================================
    if (isset($_SESSION['user_id'])) {
        $current_user_id = $_SESSION['user_id'];
        
        // Проверяем, не занят ли этот Telegram ID
        $stmt = $pdo->prepare("SELECT id FROM users WHERE telegram_id = ?");
        $stmt->execute([$tg_id]);
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            if ($existing_user['id'] != $current_user_id) {
                // КОНФЛИКТ: ID занят другим (старым) аккаунтом.
                $old_id = $existing_user['id'];

                // --- [НАЧАЛО] БЛОК ПЕРЕНОСА ДАННЫХ ---
                // Здесь мы меняем владельца записей со старого ID на новый.
                // Раскомментируй и измени названия таблиц на свои:
                
                // Пример 1: Перенос заказов
                // $pdo->prepare("UPDATE orders SET user_id = ? WHERE user_id = ?")->execute([$current_user_id, $old_id]);
                
                // Пример 2: Перенос корзины
                // $pdo->prepare("UPDATE cart SET user_id = ? WHERE user_id = ?")->execute([$current_user_id, $old_id]);
                
                // Пример 3: Перенос избранного (если есть уникальные ключи, может потребоваться IGNORE)
                // $pdo->prepare("UPDATE favorites SET user_id = ? WHERE user_id = ?")->execute([$current_user_id, $old_id]);

                // --- [КОНЕЦ] БЛОК ПЕРЕНОСА ДАННЫХ ---

                // Теперь, когда данные спасены, удаляем старый пустой аккаунт
                $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$old_id]);
            }
        }

        // Привязываем Telegram к текущему аккаунту
        $stmt = $pdo->prepare("UPDATE users SET telegram_id = ?, telegram_username = ? WHERE id = ?");
        $stmt->execute([$tg_id, $username, $current_user_id]);
        
        header('Location: profile.php?msg=tg_linked');
        exit;
    }

    // ========================================================
    // СЦЕНАРИЙ 2: ВХОД / РЕГИСТРАЦИЯ
    // ========================================================
    $stmt = $pdo->prepare("SELECT * FROM users WHERE telegram_id = ?");
    $stmt->execute([$tg_id]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: profile.php');
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, telegram_id, telegram_username) VALUES (?, ?, ?)");
        $stmt->execute([$first_name, $tg_id, $username]);
        
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $first_name;
        header('Location: profile.php');
    }

} catch (Exception $e) {
    die('Ошибка авторизации Telegram: ' . $e->getMessage());
}
?>