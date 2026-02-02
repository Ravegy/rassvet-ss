<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Контакты | РАССВЕТ-С</title>
<link rel="stylesheet" href="common.css?v=<?= time() ?>">
<link rel="stylesheet" href="pages/contacts/style.css?v=<?= time() ?>">
</head>
<body>
<?php include 'includes/header.php'; ?>
<main class="contacts-page">
<div class="container">
<div class="page-header">
<h1 class="page-title">СВЯЗАТЬСЯ С НАМИ</h1>
<div class="page-status"><span class="status-dot"></span> ВСЕГДА НА СВЯЗИ</div>
</div>
<div class="contacts-grid">
<div class="contacts-info-col">
<div class="tech-card contact-card highlight-card">
<div class="card-icon-corner"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
<h3 class="card-label">ЕДИНАЯ СПРАВОЧНАЯ</h3>
<a href="tel:+78120000000" class="main-phone">+7 (812) 000-00-00</a>
<p class="work-time">Пн-Пт: 09:00 — 18:00 (МСК)</p>
<div class="socials-row">
<a href="#" class="social-link w-app">WHATSAPP</a>
<a href="#" class="social-link tg">TELEGRAM</a>
</div>
</div>
<div class="tech-card departments-card">
<h3 class="card-label">ПРЯМЫЕ КОНТАКТЫ ОТДЕЛОВ</h3>
<div class="dept-item">
<div class="dept-head"><span class="dept-name">ОТДЕЛ ПРОДАЖ</span><span class="dept-status">Sales</span></div>
<a href="mailto:sales@rassvet-s.ru" class="dept-link">sales@rassvet-s.ru</a>
</div>
<div class="dept-item">
<div class="dept-head"><span class="dept-name">СЕРВИСНАЯ СЛУЖБА</span><span class="dept-status">Service</span></div>
<a href="mailto:service@rassvet-s.ru" class="dept-link">service@rassvet-s.ru</a>
</div>
<div class="dept-item">
<div class="dept-head"><span class="dept-name">БУХГАЛТЕРИЯ</span><span class="dept-status">Docs</span></div>
<a href="mailto:buh@rassvet-s.ru" class="dept-link">buh@rassvet-s.ru</a>
</div>
</div>
<div class="tech-card req-mini-card">
<h3 class="card-label">ЮРИДИЧЕСКАЯ ИНФОРМАЦИЯ</h3>
<div class="req-row-mini">
<span>ООО «РАССВЕТ-С»</span>
<button class="icon-btn copy-text" data-copy="ООО «РАССВЕТ-С»" title="Скопировать"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg></button>
</div>
<div class="req-row-mini">
<span>ИНН: 7805626388</span>
<button class="icon-btn copy-text" data-copy="7805626388" title="Скопировать"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg></button>
</div>
<div class="req-row-mini">
<span>КПП: 780501001</span>
<button class="icon-btn copy-text" data-copy="780501001" title="Скопировать"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg></button>
</div>
</div>
</div>
<div class="contacts-map-col">
<div class="tech-card addresses-row">
<div class="addr-col">
<div class="addr-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></div>
<span class="addr-type">ОФИС (СПб)</span>
<p>ул. Промышленная, д. 42<br><span class="addr-hint">БЦ "Технопарк", офис 305</span></p>
</div>
<div class="vertical-divider"></div>
<div class="addr-col">
<div class="addr-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
<span class="addr-type">СКЛАД (СПб)</span>
<p>ул. Лесопарковая, д. 5<br><span class="addr-hint">Заезд с грузового КПП</span></p>
</div>
</div>
<div class="tech-card map-card">
<div class="map-overlay">
<div class="map-interface">
<div class="map-grid"></div>
<div class="map-marker pulse"></div>
<div class="map-label">СКЛАД SPB-01</div>
</div>
<iframe src="https://yandex.ru/map-widget/v1/?ll=30.268715%2C59.886367&z=14" width="100%" height="100%" frameborder="0"></iframe>
</div>
</div>
<div class="tech-card form-card">
<h3 class="card-label">НАПИСАТЬ СООБЩЕНИЕ</h3>
<form class="contact-form js-send-form" enctype="multipart/form-data">
<div class="form-row">
<input type="text" name="name" class="c-input" placeholder="Ваше имя" required>
<input type="tel" name="phone" class="c-input" placeholder="Ваш телефон" required>
</div>
<div class="form-row"><input type="email" name="email" class="c-input" placeholder="Ваша почта (Email)"></div>
<textarea name="message" class="c-input c-area" placeholder="Текст сообщения или список запчастей..." rows="3"></textarea>
<div class="file-upload-wrapper">
<input type="file" name="file" id="formFile" class="file-input-hidden">
<label for="formFile" class="file-upload-label">
<span class="file-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg></span>
<span class="file-text" id="fileName">Прикрепить файл (фото, PDF, DOCX)</span>
</label>
</div>
<button type="submit" class="btn-neon">
    <span></span><span></span><span></span><span></span>
    ОТПРАВИТЬ ЗАПРОС
</button>
</form>
</div>
</div>
</div>
</div>
</main>
<?php include 'includes/footer.php'; ?>
<script src="pages/contacts/script.js?v=<?= time() ?>"></script>
</body>
</html>