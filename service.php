<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сервис и Ремонт | РАССВЕТ-С</title>
    <link rel="stylesheet" href="common.css">
    <link rel="stylesheet" href="pages/service/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<main class="service-page">
    <div class="container">
        
        <div class="page-header">
            <h1 class="page-title">СЕРВИС И РЕМОНТ</h1>
            <div class="page-status">
                <span class="status-dot"></span> БРИГАДЫ ГОТОВЫ К ВЫЕЗДУ
            </div>
        </div>

        <section class="section-block hero-service">
            <div class="tech-card hero-card">
                <div class="hero-content">
                    <div class="badge">РАБОТАЕМ ПО ВСЕЙ РОССИИ</div>
                    <h2 class="hero-title">ПРОФЕССИОНАЛЬНЫЙ РЕМОНТ <br><span class="text-yellow">ЛЕСОЗАГОТОВИТЕЛЬНОЙ ТЕХНИКИ</span></h2>
                    <p class="hero-desc">
                        Авторизованный сервисный центр. Мы понимаем цену простоя в лесу, поэтому наши мобильные бригады на базе <strong>Toyota Hilux</strong> и <strong>GAZelle NEXT</strong> укомплектованы диагностическим оборудованием, инструментом Milwaukee и автономными генераторами для ремонта любой сложности прямо на делянке.
                    </p>
                    
                    <div class="hero-features">
                        <div class="hf-item">
                            <div class="hf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg></div>
                            <span>5 сервисных экипажей</span>
                        </div>
                        <div class="hf-item">
                            <div class="hf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></div>
                            <span>Выезд в течение 24 часов</span>
                        </div>
                        <div class="hf-item">
                            <div class="hf-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg></div>
                            <span>Опыт инженеров от 7 лет</span>
                        </div>
                    </div>

                    <a href="#order-form" class="btn-neon"><span></span><span></span><span></span><span></span>ЗАКАЗАТЬ ВЫЕЗД МАСТЕРА</a>
                </div>
<div class="hero-image-decor">
    <img src="img/service-car.png" alt="Сервисный автомобиль" class="hero-img">
    
    <div class="decor-circle"></div>
</div>
            </div>
        </section>

        <section class="section-block">
            <h3 class="block-title">НАШИ КОМПЕТЕНЦИИ</h3>
            <div class="services-grid">
                <div class="tech-card service-card">
                    <div class="sc-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                    </div>
                    <h4>КОМПЬЮТЕРНАЯ ДИАГНОСТИКА</h4>
                    <p>Глубокая диагностика систем управления MaxiXplorer, TimberMatic. Чтение и расшифровка активных кодов ошибок, адаптация узлов после замены, обновление ПО харвестеров.</p>
                    <ul class="sc-list">
                        <li>Калибровка манипуляторов и харвестерных голов</li>
                        <li>Настройка параметров пиления</li>
                        <li>Поиск обрывов в CAN-шине</li>
                    </ul>
                </div>
                <div class="tech-card service-card">
                    <div class="sc-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    </div>
                    <h4>РЕМОНТ ДВС</h4>
                    <p>Капитальный и текущий ремонт двигателей SISU / AGCO Power. Собственный моторный цех. Восстановление ГБЦ, замена поршневой группы, ремонт топливной аппаратуры Common Rail.</p>
                    <ul class="sc-list">
                        <li>Регулировка клапанов</li>
                        <li>Замена турбокомпрессоров</li>
                        <li>Опрессовка системы охлаждения</li>
                    </ul>
                </div>
                <div class="tech-card service-card">
                    <div class="sc-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
                    </div>
                    <h4>ГИДРАВЛИЧЕСКИЕ СИСТЕМЫ</h4>
                    <p>Ремонт и настройка гидронасосов и гидромоторов (Danfoss, Bosch Rexroth). Изготовление РВД любого диаметра. Поиск утечек и перетечек, замер давления в контрольных точках.</p>
                    <ul class="sc-list">
                        <li>Ремонт гидрораспределителей</li>
                        <li>Замена уплотнений гидроцилиндров</li>
                        <li>Промывка гидросистемы с фильтрацией</li>
                    </ul>
                </div>
                <div class="tech-card service-card">
                    <div class="sc-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="3"></circle></svg>
                    </div>
                    <h4>ВОССТАНОВЛЕНИЕ ОТВЕРСТИЙ</h4>
                    <p>Мобильный наплавочно-расточной комплекс. Восстановление геометрии разбитых отверстий сочленения полурам, крепления манипулятора, проушин гидроцилиндров без разборки узла.</p>
                    <ul class="sc-list">
                        <li>Диапазон расточки: Ø 42-400 мм</li>
                        <li>Восстановление посадочных мест под подшипники</li>
                        <li>Сварочные работы любой сложности</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="section-block workflow-section">
            <h3 class="block-title">АЛГОРИТМ РАБОТЫ</h3>
            <div class="tech-card workflow-card">
                <div class="workflow-steps">
                    <div class="step-item">
                        <div class="step-num">01</div>
                        <h4 class="step-title">ЗАЯВКА</h4>
                        <p class="step-desc">Вы сообщаете модель техники, характер неисправности и геолокацию машины.</p>
                    </div>
                    <div class="step-arrow"></div>
                    <div class="step-item">
                        <div class="step-num">02</div>
                        <h4 class="step-title">ВЫЕЗД</h4>
                        <p class="step-desc">Инженер прибывает на объект с необходимыми запчастями и инструментом.</p>
                    </div>
                    <div class="step-arrow"></div>
                    <div class="step-item">
                        <div class="step-num">03</div>
                        <h4 class="step-title">РЕМОНТ</h4>
                        <p class="step-desc">Проводим диагностику, устраняем поломку, калибруем оборудование.</p>
                    </div>
                    <div class="step-arrow"></div>
                    <div class="step-item">
                        <div class="step-num">04</div>
                        <h4 class="step-title">ОТЧЕТ</h4>
                        <p class="step-desc">Демонстрация работоспособности, подписание акта, рекомендации.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-block">
            <div class="tech-card price-table-card">
                <div class="price-header">
                    <h3 class="card-title">СТОИМОСТЬ УСЛУГ</h3>
                    <p class="price-desc">Базовые тарифы. Окончательная смета формируется после дефектовки.</p>
                </div>
                <div class="price-table-wrapper">
                    <table class="price-table">
                        <thead>
                            <tr>
                                <th>НАИМЕНОВАНИЕ РАБОТ</th>
                                <th>ЕД. ИЗМ.</th>
                                <th>СТОИМОСТЬ (с НДС)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Выезд сервисного инженера (до 50 км от КАД)</td>
                                <td>выезд</td>
                                <td class="price-val">5 000 ₽</td>
                            </tr>
                            <tr>
                                <td>Километраж (свыше 50 км)</td>
                                <td>км</td>
                                <td class="price-val">60 ₽</td>
                            </tr>
                            <tr>
                                <td>Нормо-час работы (слесарные работы)</td>
                                <td>час</td>
                                <td class="price-val">3 500 ₽</td>
                            </tr>
                            <tr>
                                <td>Нормо-час работы (электрика/гидравлика)</td>
                                <td>час</td>
                                <td class="price-val">4 500 ₽</td>
                            </tr>
                            <tr>
                                <td>Компьютерная диагностика (без ремонта)</td>
                                <td>услуга</td>
                                <td class="price-val">15 000 ₽</td>
                            </tr>
                            <tr>
                                <td>Дефектовка гидронасоса / гидромотора</td>
                                <td>шт.</td>
                                <td class="price-val">12 000 ₽</td>
                            </tr>
                            <tr>
                                <td>Восстановление отверстий (наплавка + расточка)</td>
                                <td>отверстие</td>
                                <td class="price-val">от 8 000 ₽</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="price-footer">
                    <div class="pf-info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        <span>Гарантия на выполненные работы — 6 месяцев или 1000 моточасов.</span>
                    </div>
                    <a href="#" class="btn btn-outline download-price">СКАЧАТЬ ПРАЙС (PDF)</a>
                </div>
            </div>
        </section>

        <section class="section-block faq-section">
            <h3 class="block-title">ЧАСТЫЕ ВОПРОСЫ</h3>
            <div class="faq-grid">
                <div class="tech-card faq-item" onclick="toggleFaq(this)">
                    <div class="faq-head">
                        <h4>В какие регионы вы выезжаете?</h4>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-body">
                        <p>Мы базируемся в Санкт-Петербурге, но наши бригады регулярно работают в Ленинградской, Новгородской, Псковской областях и Республике Карелия. Для крупных ремонтов возможен выезд в любой регион РФ по согласованию.</p>
                    </div>
                </div>
                <div class="tech-card faq-item" onclick="toggleFaq(this)">
                    <div class="faq-head">
                        <h4>Какие запчасти вы используете при ремонте?</h4>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-body">
                        <p>По умолчанию мы используем оригинальные запчасти Komatsu/Valmet. По желанию заказчика можем предложить качественные аналоги (OEM) от производителей Bosch Rexroth, Parker, Danfoss, что позволяет сэкономить до 30% бюджета без потери качества.</p>
                    </div>
                </div>
                <div class="tech-card faq-item" onclick="toggleFaq(this)">
                    <div class="faq-head">
                        <h4>Можно ли оплатить с НДС?</h4>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-body">
                        <p>Да, мы работаем на Общей системе налогообложения (ОСНО) и предоставляем полный пакет закрывающих документов с выделенным НДС 20%.</p>
                    </div>
                </div>
                <div class="tech-card faq-item" onclick="toggleFaq(this)">
                    <div class="faq-head">
                        <h4>Есть ли гарантия на восстановленные узлы?</h4>
                        <span class="faq-icon">+</span>
                    </div>
                    <div class="faq-body">
                        <p>На капитальный ремонт двигателей и гидравлических насосов мы даем гарантию 6 месяцев. На работы по восстановлению отверстий гарантия распространяется на геометрию и качество наплавленного металла.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-block" id="order-form">
            <div class="tech-card form-wrapper">
                <div class="fw-left">
                    <h3 class="card-title">ЗАЯВКА НА РЕМОНТ</h3>
                    <p>Опишите проблему, укажите модель техники и местонахождение. Наш инженер свяжется с вами в течение 15 минут для предварительной консультации.</p>
                    <div class="contacts-mini">
                        <div class="cm-item">
                            <span class="cm-label">ТЕЛЕФОН СЕРВИСА:</span>
                            <a href="tel:+78120000000" class="cm-val">+7 (812) 000-00-00</a>
                        </div>
                    </div>
                </div>
                <div class="fw-right">
<form class="service-form" id="serviceForm">
    <div class="form-grid">
        <input type="text" name="name" class="c-input" placeholder="Ваше имя" required>
        <input type="tel" name="phone" class="c-input" placeholder="+7 (___) ___-__-__" required>
        
        <input type="text" name="model" class="c-input" placeholder="Модель (например, Komatsu 875)">
        <input type="text" name="location" class="c-input" placeholder="Где находится машина?">
    </div>
    
    <textarea name="message" class="c-input c-area" placeholder="Описание неисправности..." rows="3"></textarea>
    
    <button type="submit" class="btn-neon form-btn">
        <span></span><span></span><span></span><span></span>
        ОТПРАВИТЬ ЗАЯВКУ
    </button>
</form>
                </div>
            </div>
        </section>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
<script src="pages/service/script.js"></script>
</body>
</html>