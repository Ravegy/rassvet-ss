<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Выбор бренда | РАССВЕТ-С</title>
    <link rel="stylesheet" href="common.css?v=<?= time() ?>">
    <link rel="stylesheet" href="pages/catalog/style.css?v=<?= time() ?>">
</head>
<body>

<?php include 'includes/header.php'; ?>

<?php
// Получаем параметры
$title = isset($_GET['title']) ? urldecode($_GET['title']) : 'ВЫБОР БРЕНДА';
$type = isset($_GET['type']) ? $_GET['type'] : 'harvester'; // По умолчанию харвестер
?>

<main class="catalog-page">
    <div class="container">
        
        <div class="page-header">
            <h1 class="page-title"><?= htmlspecialchars($title) ?></h1>
            <a href="catalog.php" class="btn-back">
    <span></span><span></span><span></span><span></span>
    ← НАЗАД В КАТАЛОГ
</a>
        </div>

        <div class="brand-grid">
            
            <a href="models.php?brand=komatsu&type=<?= $type ?>" class="cat-item brand-item">
                <div class="cat-img">
                    <img src="img/slide1.jpg" alt="Komatsu Forest">
                    <div class="brand-overlay">KOMATSU</div>
                </div>
                <div class="cat-content">
                    <h3>ТЕХНИКА KOMATSU</h3>
                    <span class="cat-link">ВЫБРАТЬ МОДЕЛЬ</span>
                </div>
            </a>

            <a href="models.php?brand=valmet&type=<?= $type ?>" class="cat-item brand-item">
                <div class="cat-img">
                    <img src="img/valmet.jpg" alt="Valmet">
                    <div class="brand-overlay">VALMET</div>
                </div>
                <div class="cat-content">
                    <h3>ТЕХНИКА VALMET</h3>
                    <span class="cat-link">ВЫБРАТЬ МОДЕЛЬ</span>
                </div>
            </a>

        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>