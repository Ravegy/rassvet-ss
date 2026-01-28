<?php
require_once 'includes/db.php';

header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

// Ищем уникальные артикулы, похожие на ввод
// DISTINCT нужен, чтобы не показывать 10 раз один и тот же болт из разных схем
$stmt = $pdo->prepare("SELECT DISTINCT part_number, name FROM parts WHERE part_number LIKE ? OR name LIKE ? LIMIT 10");
$stmt->execute(["%$q%", "%$q%"]);
$results = $stmt->fetchAll();

echo json_encode($results);
?>