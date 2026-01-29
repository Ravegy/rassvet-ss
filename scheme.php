<?php
require_once 'includes/db.php';

$model = isset($_GET['model']) ? urldecode($_GET['model']) : '';
$scheme_id = isset($_GET['id']) ? $_GET['id'] : '';

// 1. Получаем инфо о схеме И ID РОДИТЕЛЯ (parent_id)
// Добавили parent_id в выборку
$stmt = $pdo->prepare("SELECT name, image, parent_id FROM structure WHERE cat_id = ? AND model = ?");
$stmt->execute([$scheme_id, $model]);
$scheme_info = $stmt->fetch();

$scheme_title = $scheme_info ? $scheme_info['name'] : 'Схема';
$scheme_image = (!empty($scheme_info['image'])) ? $scheme_info['image'] : 'cat-harvester.jpg';

// Логика кнопки НАЗАД:
// Если у схемы есть родитель (parent_id не 0 и не пустой), идем к нему.
// Иначе идем в корень (ROOT).
$back_id = (!empty($scheme_info['parent_id']) && $scheme_info['parent_id'] != '0') ? $scheme_info['parent_id'] : 'ROOT';

// 2. Получаем список запчастей
$stmt_parts = $pdo->prepare("SELECT * FROM parts WHERE cat_id = ? AND model = ?");
$stmt_parts->execute([$scheme_id, $model]);
$parts = $stmt_parts->fetchAll();

usort($parts, function($a, $b) {
    return (int)$a['pos_code'] <=> (int)$b['pos_code'];
});
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($scheme_title) ?> | РАССВЕТ-С</title>
    <link rel="stylesheet" href="common.css?v=<?= time() ?>">
    <link rel="stylesheet" href="pages/scheme/style.css?v=<?= time() ?>">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="scheme-page">
    <div class="container">
        
        <div class="back-nav">
            <a href="groups.php?model=<?= urlencode($model) ?>&id=<?= $back_id ?>" class="back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                НАЗАД
            </a>
        </div>

        <h1 class="scheme-title"><?= htmlspecialchars($scheme_title) ?></h1>

        <div class="scheme-grid">
            
            <div class="scheme-viewer tech-card">
                <div class="scheme-img-wrapper">
                    <img src="img/<?= htmlspecialchars($scheme_image) ?>" alt="<?= htmlspecialchars($scheme_title) ?>" class="scheme-image">
                </div>
                <div class="scheme-controls">
                    <span class="hint">Нажмите на номер позиции в таблице для поиска (в разработке)</span>
                </div>
            </div>

            <div class="parts-list tech-card">
                <h3>СПИСОК КОМПОНЕНТОВ</h3>
                
                <div class="table-responsive">
                    <table class="parts-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th style="width: 120px;">NUMBER</th>
                                <th>NAME</th>
                                <th>SPECIFICATIONS</th>
                                <th style="width: 50px;">QTY</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($parts) > 0): ?>
                                <?php foreach ($parts as $part): ?>
                                <tr>
                                    <td class="pos-cell"><span class="pos-num"><?= $part['pos_code'] ?></span></td>
                                    <td class="art-cell"><span class="part-art"><?= $part['part_number'] ?></span></td>
                                    <td><?= $part['name'] ?></td>
                                    <td style="color: #999; font-size: 12px;"><?= $part['specs'] ?></td>
                                    <td class="qty-cell"><?= $part['qty'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 20px; color: #888;">Нет запчастей в списке</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>