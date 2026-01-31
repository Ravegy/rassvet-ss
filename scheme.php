<?php
session_start();
require_once 'includes/db.php';

$model = isset($_GET['model']) ? urldecode($_GET['model']) : '';
$scheme_id = isset($_GET['id']) ? $_GET['id'] : '';
$highlight = isset($_GET['highlight']) ? $_GET['highlight'] : '';

// 1. Получаем массивы артикулов
$cur_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$cur_session_id = session_id();

// Корзина
$cart_parts = [];
$sql_c = "SELECT part_number FROM cart WHERE " . ($cur_user_id ? "user_id = ?" : "session_id = ?");
$stmt_c = $pdo->prepare($sql_c);
$stmt_c->execute([$cur_user_id ?: $cur_session_id]);
$cart_parts = $stmt_c->fetchAll(PDO::FETCH_COLUMN);

// Избранное
$fav_parts = [];
try {
    $sql_f = "SELECT part_number FROM favorites WHERE " . ($cur_user_id ? "user_id = ?" : "session_id = ?");
    $stmt_f = $pdo->prepare($sql_f);
    $stmt_f->execute([$cur_user_id ?: $cur_session_id]);
    $fav_parts = $stmt_f->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}

// Инфо о схеме
$stmt = $pdo->prepare("SELECT name, image, parent_id FROM structure WHERE cat_id = ? AND model = ?");
$stmt->execute([$scheme_id, $model]);
$scheme_info = $stmt->fetch();

$scheme_title = $scheme_info ? $scheme_info['name'] : 'Схема';
$scheme_image = (!empty($scheme_info['image'])) ? $scheme_info['image'] : 'cat-harvester.jpg';
$back_id = (!empty($scheme_info['parent_id']) && $scheme_info['parent_id'] != '0') ? $scheme_info['parent_id'] : 'ROOT';

// Хлебные крошки
$breadcrumbs = [];
$curr_id = $back_id;
while ($curr_id && $curr_id !== 'ROOT') {
    $stmt_path = $pdo->prepare("SELECT name, parent_id FROM structure WHERE cat_id = ? AND model = ?");
    $stmt_path->execute([$curr_id, $model]);
    $node = $stmt_path->fetch();
    if ($node) {
        $breadcrumbs[] = $node['name'];
        $curr_id = (!empty($node['parent_id']) && $node['parent_id'] != '0') ? $node['parent_id'] : 'ROOT';
    } else {
        break;
    }
}

// Запчасти
$stmt_parts = $pdo->prepare("SELECT * FROM parts WHERE cat_id = ? AND model = ?");
$stmt_parts->execute([$scheme_id, $model]);
$parts = $stmt_parts->fetchAll();
usort($parts, function($a, $b) { return (int)$a['pos_code'] <=> (int)$b['pos_code']; });
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
                        <button class="btn-tool" id="btn-fullscreen" title="На весь экран"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg></button>
                        <button class="btn-tool btn-close-modal" id="btn-close-modal" title="Закрыть"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                    </div>
                    <div class="toolbar-group">
                        <button class="btn-tool" id="btn-zoom-out"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>
                        <button class="btn-tool" id="btn-reset" style="width: auto; padding: 0 10px; font-size: 12px; font-weight: bold;">100%</button>
                        <button class="btn-tool" id="btn-zoom-in"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>
                    </div>
                </div>
                <div class="scheme-img-wrapper" id="scheme-wrapper">
                    <img src="img/<?= htmlspecialchars($scheme_image) ?>" alt="<?= htmlspecialchars($scheme_title) ?>" class="scheme-image" id="scheme-image">
                    <div class="scheme-watermark">
                        <div class="wm-line">ОНЛАЙН КАТАЛОГ РАССВЕТ-С</div>
                        <div class="wm-line wm-sub">RASSVET-S.RU</div>
                    </div>
                </div>
                <div class="scheme-controls"><span class="hint">Колесико / Щипок: Зум &bull; Драг: Перемещение</span></div>
            </div>

            <div class="parts-list tech-card">
                <h3>СПИСОК КОМПОНЕНТОВ</h3>
                <div class="table-responsive">
                    <table class="scheme-table">
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
                                <?php 
                                    $is_highlight = ($highlight && (string)$part['part_number'] === (string)$highlight);
                                    $row_class = $is_highlight ? 'highlight-row' : '';
                                    $row_id = $is_highlight ? 'target-part' : '';

                                    // Проверка
                                    $in_cart = in_array((string)$part['part_number'], $cart_parts);
                                    $in_fav = in_array((string)$part['part_number'], $fav_parts);
                                ?>
                                <tr class="part-row <?= $row_class ?>" id="<?= $row_id ?>">
                                    <td class="pos-cell"><span class="pos-num"><?= $part['pos_code'] ?></span></td>
                                    <td class="art-cell"><span class="part-art"><?= $part['part_number'] ?></span></td>
                                    <td><?= $part['name'] ?></td>
                                    <td style="color: #999; font-size: 12px;"><?= $part['specs'] ?></td>
                                    <td class="qty-cell"><?= $part['qty'] ?></td>
                                </tr>
                                <tr class="part-details-row">
                                    <td colspan="5">
                                        <div class="details-content">
                                            
                                            <div class="cart-container" data-art="<?= $part['part_number'] ?>" title="В корзину">
                                                <input type="checkbox" class="checkbox" <?= $in_cart ? 'checked' : '' ?>>
                                                <div class="svg-container">
                                                    
                                                    <svg viewBox="0 0 24 24" class="svg-outline" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="9" cy="21" r="1"></circle>
                                                        <circle cx="20" cy="21" r="1"></circle>
                                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                    </svg>
                                                    
                                                    <svg viewBox="0 0 24 24" class="svg-filled" fill="currentColor" stroke="none">
                                                        <circle cx="9" cy="21" r="1"></circle>
                                                        <circle cx="20" cy="21" r="1"></circle>
                                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                    </svg>
                                                    
                                                    <svg class="svg-celebrate" width="100" height="100" xmlns="http://www.w3.org/2000/svg">
                                                        <polygon points="10,10 20,20"></polygon>
                                                        <polygon points="10,50 20,50"></polygon>
                                                        <polygon points="20,80 30,70"></polygon>
                                                        <polygon points="90,10 80,20"></polygon>
                                                        <polygon points="90,50 80,50"></polygon>
                                                        <polygon points="80,80 70,70"></polygon>
                                                    </svg>
                                                </div>
                                            </div>
                                            
                                            <div class="heart-container" data-art="<?= $part['part_number'] ?>" title="В избранное">
                                                <input type="checkbox" class="checkbox" <?= $in_fav ? 'checked' : '' ?>>
                                                <div class="svg-container">
                                                    <svg viewBox="0 0 24 24" class="svg-outline" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M17.5,1.917a6.4,6.4,0,0,0-5.5,3.3,6.4,6.4,0,0,0-5.5-3.3A6.8,6.8,0,0,0,0,8.967c0,4.547,4.786,9.513,8.8,12.88a4.974,4.974,0,0,0,6.4,0C19.214,18.48,24,13.514,24,8.967A6.8,6.8,0,0,0,17.5,1.917Zm-3.585,18.4a2.973,2.973,0,0,1-3.83,0C4.947,16.006,2,11.87,2,8.967a4.8,4.8,0,0,1,4.5-5.05A4.8,4.8,0,0,1,11,8.967a1,1,0,0,0,2,0,4.8,4.8,0,0,1,4.5-5.05A4.8,4.8,0,0,1,22,8.967C22,11.87,19.053,16.006,13.915,20.313Z"></path>
                                                    </svg>
                                                    <svg viewBox="0 0 24 24" class="svg-filled" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M17.5,1.917a6.4,6.4,0,0,0-5.5,3.3,6.4,6.4,0,0,0-5.5-3.3A6.8,6.8,0,0,0,0,8.967c0,4.547,4.786,9.513,8.8,12.88a4.974,4.974,0,0,0,6.4,0C19.214,18.48,24,13.514,24,8.967A6.8,6.8,0,0,0,17.5,1.917Z"></path>
                                                    </svg>
                                                    <svg class="svg-celebrate" width="100" height="100" xmlns="http://www.w3.org/2000/svg">
                                                        <polygon points="10,10 20,20"></polygon>
                                                        <polygon points="10,50 20,50"></polygon>
                                                        <polygon points="20,80 30,70"></polygon>
                                                        <polygon points="90,10 80,20"></polygon>
                                                        <polygon points="90,50 80,50"></polygon>
                                                        <polygon points="80,80 70,70"></polygon>
                                                    </svg>
                                                </div>
                                            </div>

                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center; padding: 20px; color: #888;">Нет запчастей</td></tr>
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