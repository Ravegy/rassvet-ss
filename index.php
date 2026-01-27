<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>РАССВЕТ-С | Запчасти Komatsu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600&family=Montserrat:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="pages/home/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main>
    <section class="hero">
        <div class="container hero__grid">
            <div class="hero__content">
                <div class="status-bar"><span class="status-dot"></span> РАБОТАЕМ ПО ВСЕЙ РОССИИ</div>
                <h1 class="hero__title">ЗАПЧАСТИ ДЛЯ <br><span class="text-yellow">KOMATSU FOREST</span></h1>
                <p class="hero__desc">Оригинальные комплектующие и качественные аналоги. Ремонт и сервисное обслуживание лесозаготовительной техники.</p>
                <div class="hero__buttons">
                    <a href="#" class="btn btn-main">КАТАЛОГ</a>
                    <a href="#" class="btn btn-sec">СЕРВИС</a>
                </div>
            </div>
            
<div class="hero__image-block">
                <div class="industrial-box" id="hero-slider">
                    <div class="slider-overlay"></div>
                    
                    <div class="slide-card-container">
                        <div class="slide-card">
                            <span class="slide-card-category text-yellow">ТЕХНИКА В РАБОТЕ</span>
                            <h3 class="slide-card-title"></h3>
                            <div class="slide-card-line"></div>
                        </div>
                    </div>
                    
                    <div class="slide active" 
                         style="background-image: url('https://rare-gallery.com/thumbs/824532-Harvester-2014-17-Komatsu-911-Forests-Trees.jpg')" 
                         data-caption="KOMATSU 875">
                    </div>
                    <div class="slide" 
                         style="background-image: url('https://rare-gallery.com/thumbs/833386-2016-17-Komatsu-855-Forests-Forwarder-Red-Trunk.jpg')"
                         data-caption="HARVESTER 931XC">
                    </div>
                    <div class="slide" 
                         style="background-image: url('https://rare-gallery.com/thumbs/835335-2016-17-Komatsu-875-Forests-Forwarder-Wood-log.jpg')"
                         data-caption="FORWARDER 855">
                    </div>
                    
                    <div class="box-decor"></div>
                </div>
            </div>
            
        </div>
    </section>
    <section class="features">
        <div class="container features__grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div class="feature-text">
                    <h3 class="feature-title">СКЛАД В СПБ</h3>
                    <p class="feature-desc">90% позиций в наличии. Быстрая логистика по всей России.</p>
                </div>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="feature-text">
                    <h3 class="feature-title">ОТГРУЗКА 24/7</h3>
                    <p class="feature-desc">Отправляем груз в день обращения. Работаем без выходных.</p>
                </div>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                </div>
                <div class="feature-text">
                    <h3 class="feature-title">ГАРАНТИЯ</h3>
                    <p class="feature-desc">Только оригинальные запчасти и проверенный OEM.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
<script src="pages/home/script.js"></script>
</body>
</html>