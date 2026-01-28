<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Выбор модели | РАССВЕТ-С</title>
    <link rel="stylesheet" href="common.css?v=<?= time() ?>">
    <link rel="stylesheet" href="pages/catalog/style.css?v=<?= time() ?>">
</head>
<body>

<?php include 'includes/header.php'; ?>

<?php
$brand = isset($_GET['brand']) ? $_GET['brand'] : 'komatsu';
$type = isset($_GET['type']) ? $_GET['type'] : 'harvester';

// Структура массива теперь: ['name' => 'Модель', 'sn' => 'Серийный номер']
$models = [];
$page_title = "";

if ($brand == 'komatsu') {
    $page_title = "МОДЕЛЬНЫЙ РЯД KOMATSU";
    
    if ($type == 'harvester') {
        $models = [
            ['name' => '901.4', 'sn' => '9010041151 -'],
            ['name' => '901',   'sn' => '9010053267 -'],
            ['name' => '901',   'sn' => '9010064241 -'],
            ['name' => '901XC', 'sn' => '9013050237 -'],
            ['name' => '901XC', 'sn' => '9013061241 -'],
            ['name' => '901TX', 'sn' => '9011000128 -'],
            ['name' => '901TX.1', 'sn' => '9011011135 -'],
            ['name' => '911.4', 'sn' => '9110041151 -'],
            ['name' => '911.5', 'sn' => '9110050001 -'],
            ['name' => '911',   'sn' => '9110062167 -'],
            ['name' => '911',   'sn' => '9110073241 -'],
            ['name' => '911',   'sn' => '9112062167 -'],
            ['name' => '911',   'sn' => '9112073271 -'],
            ['name' => '931',   'sn' => '9310000125 -'],
            ['name' => '931.1', 'sn' => '9310010001 -'],
            ['name' => '931',   'sn' => '9310021067 -'],
            ['name' => '931',   'sn' => '9310033241 -'],
            ['name' => '931XC', 'sn' => '9313021208 -'],
            ['name' => '931XC', 'sn' => '9313032241 -'],
            ['name' => '941.1', 'sn' => '9410013175 -'],
            ['name' => '951',   'sn' => '9510004367 -'],
            ['name' => '951',   'sn' => '9510015241 -'],
            ['name' => '951XC', 'sn' => '9513010271 -'],
        ];
    } elseif ($type == 'forwarder') {
        // Тут пока заглушки, потом заполним так же
        $models = [
            ['name' => '855', 'sn' => 'Серия 1'],
            ['name' => '875', 'sn' => 'Серия 1'],
            ['name' => '895', 'sn' => 'Серия 2020']
        ];
    } elseif ($type == 'head') {
        $models = [
            ['name' => 'C93', 'sn' => 'Head'],
            ['name' => 'C144', 'sn' => 'Head']
        ];
    }
    
} elseif ($brand == 'valmet') {
    $page_title = "МОДЕЛЬНЫЙ РЯД VALMET";
    // Пример для Valmet
    $models = [
        ['name' => '901.3', 'sn' => 'Old Series'],
        ['name' => '911.4', 'sn' => 'Old Series']
    ];
}
?>

<main class="catalog-page">
    <div class="container">
        
        <div class="page-header">
            <h1 class="page-title"><?= $page_title ?></h1>
            <a href="#" onclick="history.back(); return false;" class="btn-back">← НАЗАД К ВЫБОРУ БРЕНДА</a>
        </div>

        <div class="models-grid">
            <?php foreach ($models as $model): ?>
                <a href="groups.php?model=<?= urlencode($model['name']) ?>&id=ROOT" class="model-card">
                    <div class="model-info">
                        <span class="model-name"><?= $model['name'] ?></span>
                        <span class="model-sn">SN: <?= $model['sn'] ?></span>
                    </div>
                    <span class="model-arrow">→</span>
                </a>
            <?php endforeach; ?>
        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>