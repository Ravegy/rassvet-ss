<?php
// Файл: api_actions.php
require_once 'includes/db.php';
session_start();

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$session_id = session_id();

// --- ИСПРАВЛЕННАЯ ФУНКЦИЯ (Без LEFT JOIN, чтобы не было дублей при выводе) ---
function getCart($pdo, $user_id, $session_id) {
    // Берем имя товара подзапросом
    $sql = "SELECT c.qty, c.part_number, 
            (SELECT name FROM parts WHERE part_number = c.part_number LIMIT 1) as name 
            FROM cart c 
            WHERE ";

    if ($user_id > 0) {
        $sql .= "c.user_id = ?";
        $params = [$user_id];
    } else {
        $sql .= "c.session_id = ? AND c.user_id = 0";
        $params = [$session_id];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

try {
    // 1. ДОБАВИТЬ В КОРЗИНУ (С защитой от дублей)
    if ($action == 'add_cart') {
        $art = $_POST['article'];
        $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

        if ($user_id > 0) {
            $sql = "INSERT INTO cart (user_id, part_number, qty) VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE qty = qty + ?";
            $pdo->prepare($sql)->execute([$user_id, $art, $qty, $qty]);
        } else {
            $sql = "INSERT INTO cart (user_id, session_id, part_number, qty) VALUES (0, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE qty = qty + ?";
            $pdo->prepare($sql)->execute([$session_id, $art, $qty, $qty]);
        }
        echo json_encode(['status' => 'success', 'cart' => getCart($pdo, $user_id, $session_id)]);

    // 2. УДАЛИТЬ ИЗ КОРЗИНЫ
    } elseif ($action == 'delete_item') {
        $art = $_POST['article'];
        if ($user_id > 0) {
            $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND part_number = ?")->execute([$user_id, $art]);
        } else {
            $pdo->prepare("DELETE FROM cart WHERE session_id = ? AND user_id = 0 AND part_number = ?")->execute([$session_id, $art]);
        }
        echo json_encode(['status' => 'success', 'cart' => getCart($pdo, $user_id, $session_id)]);

    // 3. ПОЛУЧИТЬ КОРЗИНУ
    } elseif ($action == 'get_cart') {
        echo json_encode(['status' => 'success', 'cart' => getCart($pdo, $user_id, $session_id)]);

    // 4. ИЗБРАННОЕ (Тоггл)
    } elseif ($action == 'add_fav') {
        if ($user_id == 0) {
            echo json_encode(['status' => 'error', 'message' => 'Нужна авторизация']);
            exit;
        }
        $art = $_POST['article'];
        
        $check = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND part_number = ?");
        $check->execute([$user_id, $art]);
        if($check->rowCount() > 0){
             $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND part_number = ?")->execute([$user_id, $art]);
             echo json_encode(['status' => 'removed']);
        } else {
             $pdo->prepare("INSERT INTO favorites (user_id, part_number) VALUES (?, ?)")->execute([$user_id, $art]);
             echo json_encode(['status' => 'added']);
        }

    // 5. ИЗМЕНИТЬ КОЛИЧЕСТВО
    } elseif ($action == 'update_qty') {
        $art = $_POST['article'];
        $direction = $_POST['direction']; 
        $where = ($user_id > 0) ? "user_id = ?" : "session_id = ? AND user_id = 0";
        $params = ($user_id > 0) ? [$user_id, $art] : [$session_id, $art];
        
        $stmt = $pdo->prepare("SELECT qty FROM cart WHERE $where AND part_number = ?");
        $stmt->execute($params);
        $row = $stmt->fetch();
        
        if ($row) {
            $newQty = $row['qty'];
            if ($direction == 'plus') $newQty++;
            if ($direction == 'minus') $newQty--;
            if ($newQty < 1) $newQty = 1;
            
            $sql = "UPDATE cart SET qty = ? WHERE $where AND part_number = ?";
            // Добавляем newQty в начало массива параметров
            array_unshift($params, $newQty);
            $pdo->prepare($sql)->execute($params);
        }
        echo json_encode(['status' => 'success', 'cart' => getCart($pdo, $user_id, $session_id)]);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>