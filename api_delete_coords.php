<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $article = $_POST['article'];
    $del_x = str_replace('%', '', $_POST['x']); 
    $del_y = str_replace('%', '', $_POST['y']);

    $stmt = $pdo->prepare("SELECT pos_x, pos_y FROM parts WHERE part_number = ?");
    $stmt->execute([$article]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $arr_x = explode(';', $row['pos_x']);
        $arr_y = explode(';', $row['pos_y']);
        
        $found_index = -1;

        foreach ($arr_x as $i => $val_x) {
            $val_y = isset($arr_y[$i]) ? $arr_y[$i] : '';
            
            if (trim((string)$val_x) === trim((string)$del_x) && trim((string)$val_y) === trim((string)$del_y)) {
                $found_index = $i;
                break;
            }
        }

        if ($found_index !== -1) {
            unset($arr_x[$found_index]);
            unset($arr_y[$found_index]);

            $new_str_x = implode(';', $arr_x);
            $new_str_y = implode(';', $arr_y);

            $update = $pdo->prepare("UPDATE parts SET pos_x = ?, pos_y = ? WHERE part_number = ?");
            $update->execute([$new_str_x, $new_str_y, $article]);
            
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Coord not found in DB']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Part not found']);
    }
}
?>