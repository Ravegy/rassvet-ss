<?php
// Файл: api_actions.php
require_once 'includes/db.php';
session_start();

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$session_id = session_id();

// --- ФУНКЦИЯ ПОЛУЧЕНИЯ КОРЗИНЫ ---
function getCart($pdo, $user_id, $session_id) {
    if ($user_id > 0) {
        $stmt = $pdo->prepare("SELECT c.qty, p.part_number, p.name 
                               FROM cart c 
                               LEFT JOIN parts p ON c.part_number = p.part_number 
                               WHERE c.user_id = ?");
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->prepare("SELECT c.qty, p.part_number, p.name 
                               FROM cart c 
                               LEFT JOIN parts p ON c.part_number = p.part_number 
                               WHERE c.session_id = ? AND c.user_id = 0");
        $stmt->execute([$session_id]);
    }
    return $stmt->fetchAll();
}

try {
    // 1. ДОБАВИТЬ В КОРЗИНУ
    if ($action == 'add_cart') {
        $art = $_POST['article'];
        $qty = 1;

        if ($user_id > 0) {
            $sql = "INSERT INTO cart (user_id, part_number, qty) VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE qty = qty + 1";
            $pdo->prepare($sql)->execute([$user_id, $art, $qty]);
        } else {
            $sql = "INSERT INTO cart (user_id, session_id, part_number, qty) VALUES (0, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE qty = qty + 1";
            $pdo->prepare($sql)->execute([$session_id, $art, $qty]);
        }
        echo json_encode(['status' => 'success', 'cart' => getCart($pdo, $user_id, $session_id)]);

    // 2. ПОЛУЧИТЬ КОРЗИНУ
    } elseif ($action == 'get_cart') {
        echo json_encode(['status' => 'success', 'cart' => getCart($pdo, $user_id, $session_id)]);

    // 3. ДОБАВИТЬ В ИЗБРАННОЕ
    } elseif ($action == 'add_fav') {
        if ($user_id == 0) {
            echo json_encode(['status' => 'error', 'message' => 'Нужна авторизация']);
            exit;
        }
        $art = $_POST['article'];
        $sql = "INSERT IGNORE INTO favorites (user_id, part_number) VALUES (?, ?)";
        $pdo->prepare($sql)->execute([$user_id, $art]);
        echo json_encode(['status' => 'success']);

    // 4. УДАЛИТЬ ИЗ КОРЗИНЫ (НОВОЕ)
    } elseif ($action == 'delete_item') {
        $art = $_POST['article'];
        if ($user_id > 0) {
            $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND part_number = ?")->execute([$user_id, $art]);
        } else {
            $pdo->prepare("DELETE FROM cart WHERE session_id = ? AND user_id = 0 AND part_number = ?")->execute([$session_id, $art]);
        }
        echo json_encode(['status' => 'success', 'cart' => getCart($pdo, $user_id, $session_id)]);

    // 5. ИЗМЕНИТЬ КОЛИЧЕСТВО (НОВОЕ)
    } elseif ($action == 'update_qty') {
        $art = $_POST['article'];
        $direction = $_POST['direction']; // 'plus' или 'minus'

        // Определяем условие WHERE
        $where = ($user_id > 0) ? "user_id = ?" : "session_id = ? AND user_id = 0";
        $params = ($user_id > 0) ? [$user_id, $art] : [$session_id, $art];
        
        // Получаем текущее кол-во
        $stmt = $pdo->prepare("SELECT qty FROM cart WHERE $where AND part_number = ?");
        $stmt->execute($params);
        $row = $stmt->fetch();
        
        if ($row) {
            $newQty = $row['qty'];
            if ($direction == 'plus') $newQty++;
            if ($direction == 'minus') $newQty--;

            if ($newQty < 1) $newQty = 1; // Не даем уйти в ноль кнопкой минус

            // Обновляем
            $sql = "UPDATE cart SET qty = ? WHERE $where AND part_number = ?";
            // Пересобираем параметры: qty, id, art
            $updateParams = array_merge([$newQty], $params); 
            $pdo->prepare($sql)->execute($updateParams);
        }
        
        echo json_encode(['status' => 'success', 'cart' => getCart($pdo, $user_id, $session_id)]);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>