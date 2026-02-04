<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $new_x = $_POST['x'];
    $new_y = $_POST['y'];

    $stmt = $pdo->prepare("SELECT pos_x, pos_y FROM parts WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $cur_x = (string)$row['pos_x'];
        $cur_y = (string)$row['pos_y'];

        if ($cur_x === '0' || $cur_x === '0.00') $cur_x = '';
        if ($cur_y === '0' || $cur_y === '0.00') $cur_y = '';

        if (!empty($cur_x)) {
            $final_x = $cur_x . ';' . $new_x;
            $final_y = $cur_y . ';' . $new_y;
        } else {
            $final_x = $new_x;
            $final_y = $new_y;
        }

        $update = $pdo->prepare("UPDATE parts SET pos_x = ?, pos_y = ? WHERE id = ?");
        $success = $update->execute([$final_x, $final_y, $id]);

        echo json_encode(['status' => $success ? 'success' : 'error']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Record not found']);
    }
}
?>