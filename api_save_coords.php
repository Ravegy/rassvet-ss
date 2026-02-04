<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article = $_POST['article'];
    $new_x = $_POST['x'];
    $new_y = $_POST['y'];

    // Получаем текущие координаты
    $stmt = $pdo->prepare("SELECT pos_x, pos_y FROM parts WHERE part_number = ?");
    $stmt->execute([$article]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $cur_x = (string)$row['pos_x']; // Принудительно приводим к строке
        $cur_y = (string)$row['pos_y'];

        // Очищаем от возможных нулей и мусора, если они были
        if ($cur_x === '0' || $cur_x === '0.00') $cur_x = '';
        if ($cur_y === '0' || $cur_y === '0.00') $cur_y = '';

        if (!empty($cur_x)) {
            // Если координаты уже есть, добавляем через ;
            $final_x = $cur_x . ';' . $new_x;
            $final_y = $cur_y . ';' . $new_y;
        } else {
            // Если было пусто, просто записываем новую
            $final_x = $new_x;
            $final_y = $new_y;
        }

        $update = $pdo->prepare("UPDATE parts SET pos_x = ?, pos_y = ? WHERE part_number = ?");
        $success = $update->execute([$final_x, $final_y, $article]);

        echo json_encode(['status' => $success ? 'success' : 'error', 'debug' => "$cur_x -> $final_x"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Part not found']);
    }
}
?>