<?php
// === БЛОК АВТОМАТИЧЕСКОГО ПОДКЛЮЧЕНИЯ СТИЛЕЙ ===
// Определяем путь к корню сайта от текущего скрипта
$root = __DIR__ . '/../'; 

// 1. Подключаем основной стиль common.css с кэшированием
// Проверяем дату изменения файла, чтобы сбрасывать кэш только при редактировании
$common_css_ver = file_exists($root . 'common.css') ? filemtime($root . 'common.css') : time();
echo '<link rel="stylesheet" href="common.css?v=' . $common_css_ver . '">' . PHP_EOL;

// 2. Автоматически ищем и подключаем стиль текущей страницы
// Например, для pages/about.php будет искать pages/about/style.css
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$page_style_path = 'pages/' . $current_page . '/style.css';

if (file_exists($root . $page_style_path)) {
    $page_css_ver = filemtime($root . $page_style_path);
    echo '<link rel="stylesheet" href="' . $page_style_path . '?v=' . $page_css_ver . '">' . PHP_EOL;
}
?>

<header class="header">
    <div class="container header__inner">
        <a href="/" class="logo">РАССВЕТ-С</a>
        
        <nav class="nav" id="nav-menu">
            <a href="/" class="nav__link <?= $current_page == 'index' ? 'active' : '' ?>">Главная</a>
            <a href="catalog.php" class="nav__link <?= $current_page == 'catalog' ? 'active' : '' ?>">Каталог Komatsu</a>
            <a href="about.php" class="nav__link <?= $current_page == 'about' ? 'active' : '' ?>">О Компании</a>
            <a href="delivery.php" class="nav__link <?= $current_page == 'delivery' ? 'active' : '' ?>">Доставка и Оплата</a>
            <a href="contacts.php" class="nav__link <?= $current_page == 'contacts' ? 'active' : '' ?>">Контакты</a>
        </nav>

        <div class="header__right">
            <div class="header__icons">
                
                <div class="search-wrapper">
                    <button class="icon-btn" id="search-toggle" aria-label="Поиск">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                    <div class="search-dropdown" id="search-dropdown">
                        <input type="text" id="search-input" placeholder="Введите артикул (минимум 2 символа)..." autocomplete="off">
                        <div class="search-results" id="search-results"></div>
                    </div>
                </div>

                <a href="favorites.php" class="icon-btn" aria-label="Избранное">
                     <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                </a>
                
                <div class="header-cart-wrap" style="position: relative;">
                    <a href="#" class="icon-btn header-cart-btn" id="cart-toggle" aria-label="Корзина">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        <span class="cart-badge" style="display: none;">0</span>
                    </a>
                </div>

                <a href="login.php" class="icon-btn" aria-label="Личный кабинет">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </a>
                
                <button class="burger-btn" aria-label="Меню">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

            </div>
        </div>
    </div>
</header>

<?php
$common_js_ver = file_exists($root . 'common.js') ? filemtime($root . 'common.js') : time();
?>
<script src="common.js?v=<?= $common_js_ver ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('search-toggle');
    const dropdown = document.getElementById('search-dropdown');
    const input = document.getElementById('search-input');
    const results = document.getElementById('search-results');

    if(toggle && dropdown) {
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('active');
            if(dropdown.classList.contains('active')) {
                setTimeout(() => input.focus(), 100);
            }
        });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== toggle) {
                dropdown.classList.remove('active');
            }
        });
    }

    if(input) {
        let debounceTimer;
        input.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            let val = this.value.trim();
            
            if(val.length < 2) {
                results.innerHTML = '';
                return;
            }

            // Оптимизация поиска: задержка 300мс перед запросом
            debounceTimer = setTimeout(() => {
                fetch('api_search.php?q=' + encodeURIComponent(val))
                .then(res => res.json())
                .then(data => {
                    results.innerHTML = '';
                    if(data.length === 0) {
                        results.innerHTML = '<div class="search-empty">Ничего не найдено</div>';
                    } else {
                        data.forEach(item => {
                            let link = document.createElement('a');
                            link.href = 'product.php?article=' + encodeURIComponent(item.part_number);
                            link.className = 'search-item';
                            // Безопасный вывод данных
                            link.innerHTML = `<span class="s-art">${item.part_number}</span><span class="s-name">${item.name || ''}</span>`;
                            results.appendChild(link);
                        });
                    }
                })
                .catch(err => console.error(err));
            }, 300);
        });
    }
});
</script>