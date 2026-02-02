<?php
require_once 'includes/db.php';
session_start();

header('Content-Type: application/json');

// === ЗАЩИТА (SECURITY BLOCK) ===
// 1. Принимаем только POST (кроме get_cart, если нужно)
// 2. Проверяем Referer: запрос должен прийти только с нашего сайта
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === false) {
        // Если запрос пришел не с нашего домена — блокируем
        die(json_encode(['status' => 'error', 'message' => 'Access denied']));
    }
}
// ===============================

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$session_id = session_id();

function isAdmin($pdo, $id) {
    if ($id == 0) return false;
    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() == 1;
}

function getCart($pdo, $user_id, $session_id) {
    $sql = "SELECT c.qty, c.part_number, (SELECT name FROM parts WHERE part_number = c.part_number LIMIT 1) as name FROM cart c WHERE ";
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
    if ($action == 'add_cart') {
        $art = $_POST['article'];
        $qty = max(1, (int)($_POST['qty'] ?? 1));
        
        if ($user_id > 0) {
            $sql = "INSERT INTO cart (user_id, part_number, qty) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE qty = qty + ?";
            $pdo->prepare($sql)->execute([$user_id, $art, $qty, $qty]);
        } else {
            $sql = "INSERT INTO cart (user_id, session_id, part_number, qty) VALUES (0, ?, ?, ?) ON DUPLICATE KEY UPDATE qty = qty + ?";
            $pdo->prepare($sql)->execute([$session_id, $art, $qty, $qty]);
        }
        echo json_encode(['status' => 'success', 'cart' => getCart($pdo, $user_id, $session_id)]);

    } elseif ($action == 'delete_item') {
        $art = $_POST['article'];
        if ($user_id > 0) {
            $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND part_number = ?")->execute([$user_id, $art]);
        } else {
            $pdo->prepare("DELETE FROM cart WHERE session_id = ? AND user_id = 0 AND part_number = ?")->execute([$session_id, $art]);
        }
        echo json_encode(['status' => 'success', 'cart' => getCart($pdo, $user_id, $session_id)]);

    } elseif ($action == 'get_cart') {
        echo json_encode(['status' => 'success', 'cart' => getCart($pdo, $user_id, $session_id)]);

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
            array_unshift($params, $newQty);
            $pdo->prepare($sql)->execute($params);
        }
        echo json_encode(['status' => 'success', 'cart' => getCart($pdo, $user_id, $session_id)]);

    } elseif ($action == 'save_order') {
        if ($user_id == 0) { echo json_encode(['status' => 'error', 'message' => 'Not auth']); exit; }
        
        $pdo->beginTransaction();
        
        try {
            $cart = getCart($pdo, $user_id, $session_id);
            if (empty($cart)) throw new Exception('Cart empty');

            $name = $_POST['name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $email = $_POST['email'] ?? '';
            $address = $_POST['address'] ?? '';
            $company = $_POST['company_name'] ?? '';
            $inn = $_POST['inn'] ?? '';
            $comment = $_POST['comment'] ?? '';
            
            $totalQty = 0;
            foreach($cart as $item) $totalQty += $item['qty'];

            $sqlOrder = "INSERT INTO orders (user_id, customer_name, customer_phone, customer_email, customer_address, company_name, inn, comment, total_qty, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'new')";
            $pdo->prepare($sqlOrder)->execute([$user_id, $name, $phone, $email, $address, $company, $inn, $comment, $totalQty]);
            $orderId = $pdo->lastInsertId();

            $sqlItem = "INSERT INTO order_items (order_id, part_number, name, qty) VALUES (?, ?, ?, ?)";
            $stmtItem = $pdo->prepare($sqlItem);
            foreach ($cart as $item) {
                $stmtItem->execute([$orderId, $item['part_number'], $item['name'], $item['qty']]);
            }

            $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);
            
            // Обновляем данные пользователя
            $updates = [];
            $u_params = [];
            if($phone) { $updates[] = "phone = COALESCE(NULLIF(phone, ''), ?)"; $u_params[] = $phone; }
            if($address) { $updates[] = "address = COALESCE(NULLIF(address, ''), ?)"; $u_params[] = $address; }
            if($company) { $updates[] = "company_name = COALESCE(NULLIF(company_name, ''), ?)"; $u_params[] = $company; }
            if($inn) { $updates[] = "inn = COALESCE(NULLIF(inn, ''), ?)"; $u_params[] = $inn; }
            
            if (!empty($updates)) {
                $u_sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
                $u_params[] = $user_id;
                $pdo->prepare($u_sql)->execute($u_params);
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'order_id' => $orderId]);

        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }

    } elseif ($action == 'update_profile') {
        if ($user_id == 0) exit;

        $fields = [];
        $params = [];
        // Простой сбор данных без лишней валидации для скорости
        if (isset($_POST['name'])) { $fields[] = 'name = ?'; $params[] = $_POST['name']; }
        if (isset($_POST['email'])) { $fields[] = 'email = ?'; $params[] = $_POST['email']; }
        if (isset($_POST['phone'])) { $fields[] = 'phone = ?'; $params[] = $_POST['phone']; }
        if (isset($_POST['address'])) { $fields[] = 'address = ?'; $params[] = $_POST['address']; }
        if (isset($_POST['company_name'])) { $fields[] = 'company_name = ?'; $params[] = $_POST['company_name']; }
        if (isset($_POST['inn'])) { $fields[] = 'inn = ?'; $params[] = $_POST['inn']; }

        if (!empty($fields)) {
            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
            $params[] = $user_id;
            $pdo->prepare($sql)->execute($params);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No data']);
        }

    } elseif ($action == 'update_order_status') {
        if (!isAdmin($pdo, $user_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            exit;
        }
        $order_id = $_POST['order_id'];
        $new_status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        echo json_encode(['status' => 'success']);
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>