<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог запчастей | РАССВЕТ-С</title>
    <link rel="stylesheet" href="common.css?v=<?= time() ?>">
    <link rel="stylesheet" href="pages/catalog/style.css?v=<?= time() ?>">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="catalog-page">
    <div class="container">
        
        <div class="page-header">
            <h1 class="page-title">КАТАЛОГ ЗАПЧАСТЕЙ KOMATSU FOREST</h1>
            <div class="page-status">
                <span class="status-dot"></span> БОЛЕЕ 5000 ПОЗИЦИЙ
            </div>
        </div>

        <div class="catalog-grid">
            
            <a href="brand_select.php?type=harvester&title=ЛЕСОЗАГОТОВИТЕЛЬНЫЕ МАШИНЫ" class="cat-item">
                <div class="cat-img">
                    <img src="img/cat-harvester.jpg" alt="Лесозаготовительные машины">
                </div>
                <div class="cat-content">
                    <h3>ЛЕСОЗАГОТОВИТЕЛЬНЫЕ МАШИНЫ</h3>
                    <span class="cat-link">ВЫБРАТЬ БРЕНД</span>
                </div>
            </a>

            <a href="brand_select.php?type=forwarder&title=ФОРВАРДЕР" class="cat-item">
                <div class="cat-img">
                    <img src="img/cat-forwarder.jpg" alt="Форвардер">
                </div>
                <div class="cat-content">
                    <h3>ФОРВАРДЕР</h3>
                    <span class="cat-link">ВЫБРАТЬ БРЕНД</span>
                </div>
            </a>

            <a href="brand_select.php?type=head&title=ГОЛОВКИ ЛЕСОЗАГОТОВИТЕЛЬНОЙ МАШИНЫ" class="cat-item">
                <div class="cat-img">
                    <img src="img/cat-head.jpg" alt="Головки">
                </div>
                <div class="cat-content">
                    <h3>ГОЛОВКИ ЛЕСОЗАГОТОВИТЕЛЬНОЙ МАШИНЫ</h3>
                    <span class="cat-link">ВЫБРАТЬ БРЕНД</span>
                </div>
            </a>

            <a href="brand_select.php?type=tracked&title=ГУСЕНИЧНЫЙ КОМБАЙН" class="cat-item">
                <div class="cat-img">
                    <img src="img/cat-tracked.jpg" alt="Гусеничный комбайн">
                </div>
                <div class="cat-content">
                    <h3>ГУСЕНИЧНЫЙ КОМБАЙН</h3>
                    <span class="cat-link">ВЫБРАТЬ БРЕНД</span>
                </div>
            </a>

            <a href="#" class="cat-item">
                <div class="cat-img">
                    <img src="img/cat-accessories.jpg" alt="Аксессуары">
                </div>
                <div class="cat-content">
                    <h3>АКСЕССУАРЫ</h3>
                    <span class="cat-link">ПЕРЕЙТИ В РАЗДЕЛ</span>
                </div>
            </a>

            <a href="#" class="cat-item">
                <div class="cat-img">
                    <img src="img/cat-equipment.jpg" alt="Оборудование">
                </div>
                <div class="cat-content">
                    <h3>ОБОРУДОВАНИЕ</h3>
                    <span class="cat-link">ПЕРЕЙТИ В РАЗДЕЛ</span>
                </div>
            </a>

        </div>

        <div class="tech-card vin-request-block">
            <div class="vr-content">
                <h2>НЕ НАШЛИ НУЖНУЮ ДЕТАЛЬ?</h2>
                <p>На складе более 5000 позиций. Оставьте запрос по VIN-номеру или артикулу, и мы подберем запчасть за 15 минут.</p>
                <form class="vin-form-row">
                    <input type="text" class="c-input" placeholder="Введите артикул или название..." required>
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