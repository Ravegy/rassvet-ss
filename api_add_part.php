<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat_id = $_POST['cat_id'];
    $model = $_POST['model'];
    $pos_code = $_POST['pos_code'];
    $part_number = $_POST['part_number'];
    $name = $_POST['name'];
    $specs = $_POST['specs'];
    $qty = $_POST['qty'];

    if (empty($part_number) || empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Артикул и Название обязательны']);
        exit;
    }

    $sql = "INSERT INTO parts (cat_id, model, pos_code, part_number, name, specs, qty, pos_x, pos_y) 
            VALUES (?, ?, ?, ?, ?, ?, ?, '', '')";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$cat_id, $model, $pos_code, $part_number, $name, $specs, $qty]);

    if ($result) {
        echo json_encode(['status' => 'success', 'id' => $pdo->lastInsertId()]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ошибка записи в БД']);
    }
}
?>