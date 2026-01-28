<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Запчасти для <?= htmlspecialchars($_GET['model'] ?? 'техники') ?> | РАССВЕТ-С</title>
    <link rel="stylesheet" href="common.css?v=<?= time() ?>">
    <link rel="stylesheet" href="pages/catalog/style.css?v=<?= time() ?>">
</head>
<body>

<?php include 'includes/header.php'; ?>

<?php
// Получаем модель из ссылки
$model = isset($_GET['model']) ? urldecode($_GET['model']) : 'МОДЕЛЬ НЕ ВЫБРАНА';

// Список групп запчастей (Полный)
$groups = [
    ['id' => '1000000', 'name' => 'ДВИГАТЕЛЬ',         'img' => 'engine.png'],
    ['id' => '2000000', 'name' => 'ШАССИ',             'img' => 'chassis.png'],
    ['id' => '3000000', 'name' => 'КОРОБКА ПЕРЕДАЧ',   'img' => 'transmission.png'],
    ['id' => '4000000', 'name' => 'ТОРМОЗА',           'img' => 'brakes.png'],
    ['id' => '5000000', 'name' => 'РУЛЕВОЕ УПРАВЛЕНИЕ','img' => 'steering.png'],
    ['id' => '6000000', 'name' => 'КАБИНА',            'img' => 'cabin.png'],
    ['id' => '7000000', 'name' => 'СИСТЕМА УПРАВЛЕНИЯ','img' => 'control.png'],
    ['id' => '7500000', 'name' => 'ГИДРАВЛИКА',        'img' => 'hydraulics.png'],
    
    // --- ГРУППА КРАНОВ ---
    ['id' => '8000000', 'name' => 'ПОДЪЕМНЫЙ КРАН',     'img' => 'crane.png'],
    ['id' => '8000000', 'name' => 'ПОДЪЕМНЫЙ КРАН 200H', 'img' => 'crane_200h.png'], // НОВЫЙ
    ['id' => '8000000', 'name' => 'ПОДЪЕМНЫЙ КРАН 230H', 'img' => 'crane_230h.png'],
    ['id' => '8000000', 'name' => 'ПОДЪЕМНЫЙ КРАН 270H', 'img' => 'crane_270h.png'], // НОВЫЙ
    
    ['id' => '8500000', 'name' => 'ЛЕБЕДКА',            'img' => 'winch.png'],
    
    ['id' => '9000000', 'name' => 'ЛЕСОЗАГОТОВИТЕЛЬНОЕ ОБОРУДОВАНИЕ', 'img' => 'harvesting.png'],
    ['id' => '9990000', 'name' => 'РАЗЛИЧНОЕ ОБОРУДОВАНИЕ', 'img' => 'cat_misc.png'],
];
?>

<main class="catalog-page">
    <div class="container">
        
        <div class="page-header">
            <h1 class="page-title">ЗАПЧАСТИ ДЛЯ <?= htmlspecialchars($model) ?></h1>
            <a href="#" onclick="history.back(); return false;" class="btn-back">← НАЗАД К ВЫБОРУ МОДЕЛИ</a>
        </div>

        <div class="catalog-grid">
            
            <?php foreach ($groups as $group): ?>
                <a href="scheme_demo.php?model=<?= urlencode($model) ?>&group_id=<?= $group['id'] ?>" class="cat-item">
                    <div class="cat-img group-img">
                        <img src="img/<?= $group['img'] ?>" alt="<?= $group['name'] ?>">
                    </div>
                    <div class="cat-content">
                        <span style="font-size: 10px; color: #555;"><?= $group['id'] ?></span>
                        <h3><?= $group['name'] ?></h3>
                        <span class="cat-link">ПЕРЕЙТИ В РАЗДЕЛ</span>
                    </div>
                </a>
            <?php endforeach; ?>

        </div>
        
        <div class="tech-card vin-request-block">
            <div class="vr-content">
                <h2>НЕ НАШЛИ НУЖНУЮ ДЕТАЛЬ В КАТАЛОГЕ?</h2>
                <p>Для модели <b><?= $model ?></b> у нас есть больше позиций на складе. Оставьте запрос.</p>
                <form class="vin-form-row">
                    <input type="text" class="c-input" placeholder="Введите артикул..." required>
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