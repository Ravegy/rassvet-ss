<?php
require_once 'includes/db.php'; // Подключаем БД

$model = isset($_GET['model']) ? urldecode($_GET['model']) : 'Техника';
$current_id = isset($_GET['id']) ? $_GET['id'] : 'ROOT';

// 1. Логика для заголовка и кнопки НАЗАД
$current_name = "Каталог";
$back_url = "models.php"; // Если мы в ROOT, назад ведет к выбору модели

if ($current_id !== 'ROOT') {
    // ВАЖНО: Добавили parent_id в запрос, чтобы знать, куда возвращаться
    $stmt = $pdo->prepare("SELECT name, is_scheme, parent_id FROM structure WHERE cat_id = ? AND model = ?");
    $stmt->execute([$current_id, $model]);
    $cat_info = $stmt->fetch();
    
    if ($cat_info) {
        $current_name = $cat_info['name'];
        
        // Редирект, если это схема (защита от случайного попадания)
        if ($cat_info['is_scheme'] == 1) {
            header("Location: scheme.php?model=" . urlencode($model) . "&id=" . $current_id);
            exit;
        }

        // Вычисляем ссылку НАЗАД
        // Если parent_id пустой или 0, значит родитель — это корень (ROOT)
        $parent_id = (!empty($cat_info['parent_id']) && $cat_info['parent_id'] != '0') ? $cat_info['parent_id'] : 'ROOT';
        $back_url = "groups.php?model=" . urlencode($model) . "&id=" . $parent_id;
    }
}

// 2. Ищем подкатегории в базе
$stmt = $pdo->prepare("SELECT * FROM structure WHERE parent_id = ? AND model = ? ORDER BY record_id ASC");
$stmt->execute([$current_id, $model]);
$subcategories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($current_name) ?> | РАССВЕТ-С</title>
    <link rel="stylesheet" href="common.css?v=<?= time() ?>">
    <link rel="stylesheet" href="pages/catalog/style.css?v=<?= time() ?>">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="catalog-page">
    <div class="container">
        
        <div class="page-header">
            <h1 class="page-title"><?= htmlspecialchars($current_name) ?> <span style="color:#666; font-size: 0.6em;">(<?= htmlspecialchars($model) ?>)</span></h1>
            <a href="<?= htmlspecialchars($back_url) ?>" class="btn-back">← НАЗАД</a>
        </div>

        <?php if (!empty($subcategories)): ?>
            <div class="catalog-grid">
                <?php foreach ($subcategories as $item): ?>
                    <?php 
                        // Если это папка (is_scheme=0) -> ведем на groups.php
                        // Если это схема (is_scheme=1) -> ведем на scheme.php
                        $link = ($item['is_scheme'] == 1) 
                            ? "scheme.php?model=" . urlencode($model) . "&id=" . $item['cat_id']
                            : "groups.php?model=" . urlencode($model) . "&id=" . $item['cat_id'];
                    ?>
                    <a href="<?= $link ?>" class="cat-item">
                        <div class="cat-img group-img">
                            <img src="img/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        </div>
                        <div class="cat-content">
                            <span><?= $item['cat_id'] ?></span>
                            <h3><?= htmlspecialchars($item['name']) ?></h3>
                            <span class="cat-link">ОТКРЫТЬ</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align:center; padding: 50px; color: #888;">
                <h3>В этом разделе пока пусто</h3>
                <p>Мы заполняем каталог. Попробуйте другой раздел или оставьте заявку.</p>
            </div>
        <?php endif; ?>
        
        <div class="tech-card vin-request-block">
            <div class="vr-content">
                <h2>НЕ НАШЛИ НУЖНУЮ ДЕТАЛЬ?</h2>
                <form class="vin-form-row">
                    <input type="text" class="c-input" placeholder="Артикул..." required>
                    <input type="tel" class="c-input" placeholder="+7 (___) ___-__-__" required>
                    <button type="submit" class="btn">ОТПРАВИТЬ ЗАПРОС</button>
                </form>
            </div>
        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>