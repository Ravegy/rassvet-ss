<?php
require_once 'includes/db.php';

$art = isset($_GET['article']) ? urldecode($_GET['article']) : '';

// 1. Получаем данные о запчасти
$stmt = $pdo->prepare("SELECT * FROM parts WHERE part_number = ? LIMIT 1");
$stmt->execute([$art]);
$part_info = $stmt->fetch();

if (!$part_info) {
    die("<div style='padding:100px; text-align:center; color:#fff;'>Запчасть не найдена</div>");
}

// 2. Ищем, где она используется
$stmt_usage = $pdo->prepare("
    SELECT p.model, p.cat_id, p.pos_code, s.name as scheme_name
    FROM parts p
    LEFT JOIN structure s ON p.cat_id = s.cat_id AND p.model = s.model
    WHERE p.part_number = ?
    ORDER BY p.model ASC
");
$stmt_usage->execute([$art]);
$usage_list = $stmt_usage->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($part_info['name']) ?> - <?= $art ?> | РАССВЕТ-С</title>
    
    <link rel="stylesheet" href="common.css?v=<?= time() ?>">
    <link rel="stylesheet" href="pages/product/style.css?v=<?= time() ?>">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="product-page">
    <div class="container">
        
        <div class="breadcrumbs fade-up" style="margin-bottom: 20px; color:#666; font-size:13px;">
            <a href="catalog.php" style="color:#fff; text-decoration:none;">Каталог</a> / 
            <span>Поиск</span> / 
            <span style="color:#ff3333;"><?= htmlspecialchars($art) ?></span>
        </div>

        <h1 class="scheme-title fade-up" style="color:#fff; text-transform:uppercase; font-size:28px; margin-bottom:30px;">КАРТОЧКА ТОВАРА</h1>

        <div class="prod-grid">
            
            <div class="prod-card padded fade-up">
                <div class="prod-label">АРТИКУЛ (NUMBER)</div>
                <div class="prod-value main-art"><?= htmlspecialchars($part_info['part_number']) ?></div>

                <div class="prod-label">НАИМЕНОВАНИЕ (NAME)</div>
                <div class="prod-value"><?= htmlspecialchars($part_info['name']) ?></div>

                <div class="prod-label">ХАРАКТЕРИСТИКИ (SPECS)</div>
                <div class="prod-value"><?= htmlspecialchars($part_info['specs'] ?: 'Нет данных') ?></div>
                
                <button class="btn">ЗАПРОСИТЬ ЦЕНУ</button>
            </div>

            <div class="prod-card fade-up">
                <div class="usage-header">Применяемость в технике</div>
                
                <?php if (count($usage_list) > 0): ?>
                    <div class="table-responsive">
                        <table class="usage-table">
                            <thead>
                                <tr>
                                    <th>МОДЕЛЬ</th>
                                    <th>УЗЕЛ (СХЕМА)</th>
                                    <th>ПОЗ.</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usage_list as $use): ?>
                                <tr>
                                    <td style="font-weight:bold; color:#fff;"><?= $use['model'] ?></td>
                                    <td><?= $use['scheme_name'] ?: 'Схема '.$use['cat_id'] ?></td>
                                    <td style="text-align:center;"><?= $use['pos_code'] ?></td>
                                    <td style="text-align:right;">
                                        <a href="scheme.php?model=<?= urlencode($use['model']) ?>&id=<?= $use['cat_id'] ?>" class="usage-link">ПЕРЕЙТИ →</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="padding:20px; color:#888; font-size:13px;">Информации о применяемости нет.</div>
                <?php endif; ?>
            </div>

        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Находим все элементы с классом fade-up
    const observerOptions = {
        threshold: 0.1 // Элемент начнет появляться, когда покажется на 10%
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible'); // Добавляем класс, который запускает CSS-анимацию
                observer.unobserve(entry.target); // Перестаем следить после появления
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-up').forEach(el => {
        observer.observe(el);
    });
});
</script>

</body>
</html>