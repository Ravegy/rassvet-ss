<?php
require_once 'includes/db.php';

$model = isset($_GET['model']) ? urldecode($_GET['model']) : '';
$scheme_id = isset($_GET['id']) ? $_GET['id'] : '';

// 1. Получаем инфо о схеме: Название И Картинку
// РАНЬШЕ БЫЛО: SELECT name FROM...
// ТЕПЕРЬ: SELECT name, image FROM...
$stmt = $pdo->prepare("SELECT name, image FROM structure WHERE cat_id = ? AND model = ?");
$stmt->execute([$scheme_id, $model]);
$scheme_info = $stmt->fetch();

$scheme_title = $scheme_info ? $scheme_info['name'] : 'Схема';

// Логика картинки: Если в базе пусто, ставим заглушку cat-harvester.jpg
$scheme_image = (!empty($scheme_info['image'])) ? $scheme_info['image'] : 'cat-harvester.jpg';

// 2. Получаем список запчастей
$stmt_parts = $pdo->prepare("SELECT * FROM parts WHERE cat_id = ? AND model = ? ORDER BY pos_code ASC");
$stmt_parts->execute([$scheme_id, $model]);
$parts = $stmt_parts->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($scheme_title) ?> | РАССВЕТ-С</title>
    <link rel="stylesheet" href="common.css?v=<?= time() ?>">
    <link rel="stylesheet" href="pages/scheme/style.css?v=<?= time() ?>">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="scheme-page">
    <div class="container">
        
        <div class="breadcrumbs">
            <a href="catalog.php">Каталог</a> / 
            <span><?= htmlspecialchars($model) ?></span> /
            <span><?= htmlspecialchars($scheme_title) ?></span>
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
                                <th style="width: 50px;"></th>
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
                                    
                                    <td>
                                        <button class="btn-req icon-btn" title="Запросить">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align:center; padding: 20px; color: #888;">Нет запчастей в списке</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="parts-footer">
                    <button class="btn btn-full">ЗАПРОСИТЬ ВЕСЬ СПИСОК</button>
                </div>
            </div>

        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>