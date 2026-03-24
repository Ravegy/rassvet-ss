<?php
require_once 'includes/db.php'; // Подключаем БД

$model = isset($_GET['model']) ? urldecode($_GET['model']) : 'Техника';
$current_id = isset($_GET['id']) ? $_GET['id'] : 'ROOT';

// --- 1. ОПРЕДЕЛЯЕМ ТИП И БРЕНД (для крошек) ---
$detected_brand = 'komatsu';
$detected_type = 'harvester'; 
$detected_type_name = 'ЛЕСОЗАГОТОВИТЕЛЬНЫЕ МАШИНЫ';

if (preg_match('/^8\d+/', $model)) {
    $detected_type = 'forwarder';
    $detected_type_name = 'ФОРВАРДЕР';
} elseif (preg_match('/^C\d+/', $model)) {
    $detected_type = 'head';
    $detected_type_name = 'ГОЛОВКИ';
}

if ($model == '901.3') {
    $detected_brand = 'valmet';
}
// ----------------------------------------------

$current_name = "КАТАЛОГ ЗАПЧАСТЕЙ";
$back_url = "models.php?brand=$detected_brand&type=$detected_type";

if ($current_id !== 'ROOT') {
    $stmt = $pdo->prepare("SELECT name, is_scheme, parent_id FROM structure WHERE cat_id = ? AND model = ?");
    $stmt->execute([$current_id, $model]);
    $cat_info = $stmt->fetch();
    
    if ($cat_info) {
        $current_name = $cat_info['name'];
        
        if ($cat_info['is_scheme'] == 1) {
            header("Location: scheme.php?model=" . urlencode($model) . "&id=" . $current_id);
            exit;
        }

        // === ЛОГИКА МУЛЬТИ-РОДИТЕЛЯ ДЛЯ КНОПКИ НАЗАД ===
        // Если parent_id = "2070100,2070110", берем только ПЕРВЫЙ (основной) для возврата
        $parents_list = explode(',', $cat_info['parent_id']);
        $main_parent = trim($parents_list[0]); // Берем первый ID и убираем пробелы

        $parent_id = (!empty($main_parent) && $main_parent != '0') ? $main_parent : 'ROOT';
        $back_url = "groups.php?model=" . urlencode($model) . "&id=" . $parent_id;
    }
}

// --- 2. ХЛЕБНЫЕ КРОШКИ (С поддержкой мульти-родителей) ---
$breadcrumbs = [];
// Берем родителя текущей категории (опять же первого из списка)
$curr_crumb_id = ($current_id !== 'ROOT') ? 
    (!empty($cat_info['parent_id']) ? trim(explode(',', $cat_info['parent_id'])[0]) : 'ROOT') 
    : false;

if ($curr_crumb_id && $curr_crumb_id !== 'ROOT') {
    $temp_id = $curr_crumb_id;
    // Лимит 10, чтобы избежать бесконечных циклов при ошибках в БД
    $limit = 0; 
    while ($temp_id && $temp_id !== 'ROOT' && $limit < 10) {
        $stmt_path = $pdo->prepare("SELECT name, parent_id FROM structure WHERE cat_id = ? AND model = ?");
        $stmt_path->execute([$temp_id, $model]);
        $node = $stmt_path->fetch();
        if ($node) {
            $breadcrumbs[] = ['id' => $temp_id, 'name' => $node['name']];
            
            // Снова берем первого родителя из списка, если их несколько
            $p_list = explode(',', $node['parent_id']);
            $p_main = trim($p_list[0]);
            
            $temp_id = (!empty($p_main) && $p_main != '0') ? $p_main : 'ROOT';
        } else {
            break;
        }
        $limit++;
    }
    $breadcrumbs = array_reverse($breadcrumbs);
}

// --- 3. ИЩЕМ ПОДКАТЕГОРИИ (ГЛАВНОЕ ИЗМЕНЕНИЕ) ---
// Используем FIND_IN_SET, чтобы найти текущий ID в списке родителей
// REPLACE убирает пробелы, чтобы "100, 200" работало так же как "100,200"
$stmt = $pdo->prepare("SELECT * FROM structure WHERE FIND_IN_SET(?, REPLACE(parent_id, ' ', '')) > 0 AND model = ? ORDER BY record_id ASC");
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
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="catalog-page">
    <div class="container">
        
        <div class="smart-breadcrumbs">
            <div class="sb-list">
                <a href="catalog.php" class="sb-item" title="Каталог">Каталог</a>
                <span class="sb-sep">></span>

                <a href="brand_select.php?type=<?= $detected_type ?>&title=<?= urlencode($detected_type_name) ?>" class="sb-item" title="<?= $detected_type_name ?>">
                    <?= $detected_type_name ?>
                </a>
                <span class="sb-sep">></span>

                <a href="models.php?brand=<?= $detected_brand ?>&type=<?= $detected_type ?>" class="sb-item" title="<?= strtoupper($detected_brand) ?>">
                    <?= strtoupper($detected_brand) ?>
                </a>
                <span class="sb-sep">></span>

                <?php if ($current_id == 'ROOT'): ?>
                    <span class="sb-item active" title="<?= htmlspecialchars($model) ?>"><?= htmlspecialchars($model) ?></span>
                <?php else: ?>
                    <a href="groups.php?model=<?= urlencode($model) ?>&id=ROOT" class="sb-item" title="<?= htmlspecialchars($model) ?>">
                        <?= htmlspecialchars($model) ?>
                    </a>
                    <span class="sb-sep">></span>
                    
                    <?php foreach ($breadcrumbs as $crumb): ?>
                        <a href="groups.php?model=<?= urlencode($model) ?>&id=<?= $crumb['id'] ?>" class="sb-item" title="<?= htmlspecialchars($crumb['name']) ?>">
                            <?= htmlspecialchars($crumb['name']) ?>
                        </a>
                        <span class="sb-sep">></span>
                    <?php endforeach; ?>

                    <span class="sb-item active" title="<?= htmlspecialchars($current_name) ?>">
                        <?= htmlspecialchars($current_name) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="page-header-styled">
            <span class="page-sub-label">МОДЕЛЬ <?= htmlspecialchars($model) ?></span>
            <h1 class="page-title"><?= htmlspecialchars($current_name) ?></h1>
        </div>

        <?php if (!empty($subcategories)): ?>
            <div class="catalog-grid">
                <?php foreach ($subcategories as $item): ?>
                    <?php 
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