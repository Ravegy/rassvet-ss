<?php
// send.php - Версия для InfinityFree (Direct IP)
header('Content-Type: application/json');

// Ограничения
$MAX_FILE_SIZE = 10 * 1024 * 1024; 
$ALLOWED_TYPES = ['image/jpeg', 'image/png', 'application/pdf', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

try {
    if (!file_exists(__DIR__ . '/config.php')) {
        throw new Exception('Файл config.php не найден!');
    }
    $config = require __DIR__ . '/config.php';
    
    if (empty($config['tg_token']) || empty($config['tg_chat_id'])) {
        throw new Exception('Ошибка конфига');
    }

    $name = isset($_POST['name']) ? trim($_POST['name']) : 'Без имени';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (strlen($phone) < 5) throw new Exception('Укажите телефон');

    $txt = "<b>🔔 ЗАЯВКА (INFINITY)</b>\n";
    $txt .= "--------------------------------\n";
    $txt .= "👤 <b>Имя:</b> " . htmlspecialchars($name) . "\n";
    $txt .= "📞 <b>Телефон:</b> " . htmlspecialchars($phone) . "\n";
    if (!empty($message)) $txt .= "💬 <b>Инфо:</b> " . htmlspecialchars($message) . "\n";

    $endpoint = 'sendMessage';
    $post_fields = [
        'chat_id' => $config['tg_chat_id'],
        'parse_mode' => 'HTML',
        'text' => $txt
    ];

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file'];
        if ($file['size'] > $MAX_FILE_SIZE) throw new Exception('Файл > 10Мб');
        if (!in_array($file['type'], $ALLOWED_TYPES)) throw new Exception('Формат не поддерживается');

        $endpoint = 'sendDocument';
        $post_fields['caption'] = $txt;
        $post_fields['document'] = new CURLFile($file['tmp_name'], $file['type'], $file['name']);
        unset($post_fields['text']);
    }

    // === МАГИЯ ДЛЯ INFINITY FREE ===
    
    // 1. Используем ПРЯМОЙ IP адрес Телеграма, чтобы обойти ошибку DNS
    // Вместо api.telegram.org пишем 149.154.167.220
    $url = "https://149.154.167.220/bot{$config['tg_token']}/{$endpoint}";
    
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type:multipart/form-data",
        "Host: api.telegram.org" // Важно! Говорим серверу, что мы стучимся именно к Телеграму
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    
    // Отключаем проверки SSL, так как мы идем по IP и сертификат не совпадет
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    // Тайм-ауты
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $result = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error) {
        throw new Exception("Ошибка cURL: $error");
    }
    
    $response = json_decode($result, true);
    
    // Если Telegram вернул 200 OK
    if ($http_code == 200 && $response && $response['ok']) {
        echo json_encode(['status' => 'success', 'message' => 'Успешно отправлено!']);
    } else {
        // Если ошибка
        $desc = $response['description'] ?? 'Неизвестная ошибка';
        throw new Exception("Ошибка API ($http_code): $desc");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>