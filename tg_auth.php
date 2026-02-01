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
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE telegram_id = ?");
        $stmt->execute([$tg_id]);
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            if ($existing_user['id'] != $current_user_id) {
                // Если нужно перенести данные со старого аккаунта ТГ на текущий — допиши логику здесь
                $old_id = $existing_user['id'];
                $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$old_id]);
            }
        }

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
        // ВХОД СУЩЕСТВУЮЩЕГО
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];

        // [ВАЖНО] ПЕРЕНОС КОРЗИНЫ ГОСТЯ -> ПОЛЬЗОВАТЕЛЮ
        $current_session = session_id();
        $pdo->prepare("UPDATE cart SET user_id = ? WHERE session_id = ? AND user_id = 0")
            ->execute([$user['id'], $current_session]);

        header('Location: profile.php');

    } else {
        // РЕГИСТРАЦИЯ НОВОГО
        $stmt = $pdo->prepare("INSERT INTO users (name, telegram_id, telegram_username) VALUES (?, ?, ?)");
        $stmt->execute([$first_name, $tg_id, $username]);
        
        $new_user_id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['user_name'] = $first_name;

        // [ВАЖНО] ПЕРЕНОС КОРЗИНЫ ГОСТЯ -> ПОЛЬЗОВАТЕЛЮ
        $current_session = session_id();
        $pdo->prepare("UPDATE cart SET user_id = ? WHERE session_id = ? AND user_id = 0")
            ->execute([$new_user_id, $current_session]);
        
        header('Location: profile.php');
    }

} catch (Exception $e) {
    die('Ошибка авторизации Telegram: ' . $e->getMessage());
}
?>