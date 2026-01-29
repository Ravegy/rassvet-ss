<?php
require_once 'includes/db.php';

$model = isset($_GET['model']) ? urldecode($_GET['model']) : '';
$scheme_id = isset($_GET['id']) ? $_GET['id'] : '';

$stmt = $pdo->prepare("SELECT name, image, parent_id FROM structure WHERE cat_id = ? AND model = ?");
$stmt->execute([$scheme_id, $model]);
$scheme_info = $stmt->fetch();

$scheme_title = $scheme_info ? $scheme_info['name'] : 'Схема';
$scheme_image = (!empty($scheme_info['image'])) ? $scheme_info['image'] : 'cat-harvester.jpg';

$back_id = (!empty($scheme_info['parent_id']) && $scheme_info['parent_id'] != '0') ? $scheme_info['parent_id'] : 'ROOT';

// --- СОБИРАЕМ ПУТЬ (ОТ МЛАДШЕГО К СТАРШЕМУ) ---
$breadcrumbs = [];
$curr_id = $back_id;

while ($curr_id && $curr_id !== 'ROOT') {
    $stmt_path = $pdo->prepare("SELECT name, parent_id FROM structure WHERE cat_id = ? AND model = ?");
    $stmt_path->execute([$curr_id, $model]);
    $node = $stmt_path->fetch();
    
    if ($node) {
        // ИЗМЕНЕНИЕ: Добавляем в конец массива ($breadcrumbs[]), а не в начало (array_unshift)
        // Теперь порядок будет: [Впуск] -> [Двигатель]
        $breadcrumbs[] = $node['name'];
        
        $curr_id = (!empty($node['parent_id']) && $node['parent_id'] != '0') ? $node['parent_id'] : 'ROOT';
    } else {
        break;
    }
}
// ----------------------------------------------------

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

        <div class="scheme-header">
            <div class="scheme-number">
                № <?= htmlspecialchars($scheme_id) ?>
                
                <?php foreach ($breadcrumbs as $crumb): ?>
                    <span style="color: #666; margin: 0 5px;">/</span> 
                    <span style="color: #ccc;"><?= htmlspecialchars($crumb) ?></span>
                <?php endforeach; ?>

                <span style="color: #666; margin: 0 5px;">/</span> 
                <span style="color: #fff;"><?= htmlspecialchars($model) ?></span>
            </div>
            
            <h1 class="scheme-title"><?= htmlspecialchars($scheme_title) ?></h1>
        </div>

        <div class="scheme-grid">
            
            <div class="scheme-viewer tech-card" id="scheme-viewer-container">
                
                <div class="scheme-toolbar">
                    <div class="toolbar-group">
                        <button class="btn-tool" id="btn-fullscreen" title="На весь экран">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                        </button>
                        <button class="btn-tool btn-close-modal" id="btn-close-modal" title="Закрыть">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                    <div class="toolbar-group">
                        <button class="btn-tool" id="btn-zoom-out" title="Уменьшить">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                        <button class="btn-tool" id="btn-reset" title="Сбросить" style="width: auto; padding: 0 10px; font-size: 12px; font-weight: bold;">
                            100%
                        </button>
                        <button class="btn-tool" id="btn-zoom-in" title="Увеличить">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                    </div>
                </div>

                <div class="scheme-img-wrapper" id="scheme-wrapper">
                    <img src="img/<?= htmlspecialchars($scheme_image) ?>" alt="<?= htmlspecialchars($scheme_title) ?>" class="scheme-image" id="scheme-image">
                </div>
                
                <div class="scheme-controls">
                    <span class="hint">Колесико / Щипок: Зум &bull; Драг: Перемещение</span>
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

<script src="pages/scheme/script.js?v=<?= time() ?>"></script>

</body>
</html>