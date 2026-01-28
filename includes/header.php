<header class="header">
    <div class="container header__inner">
        <a href="/" class="logo">РАССВЕТ-С</a>
        
        <nav class="nav" id="nav-menu">
            <a href="/" class="nav__link active">Главная</a>
            <a href="catalog.php" class="nav__link">Каталог Komatsu</a>
            <a href="about.php" class="nav__link">О Компании</a>
            <a href="delivery.php" class="nav__link">Доставка и Оплата</a>
            <a href="contacts.php" class="nav__link">Контакты</a>
        </nav>

        <div class="header__right">
            <div class="header__icons">
                
                <div class="search-wrapper">
                    <button class="icon-btn" id="search-toggle" aria-label="Поиск">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.031 16.617l4.283 4.282-1.415 1.415-4.282-4.283A8.96 8.96 0 0 1 11 20c-4.968 0-9-4.032-9-9s4.032-9 9-9 9 4.032 9 9a8.96 8.96 0 0 1-1.969 5.617zm-2.006-.742A6.977 6.977 0 0 0 18 11c0-3.868-3.133-7-7-7-3.868 0-7 3.132-7 7 0 3.867 3.132 7 7 7a6.977 6.977 0 0 0 4.875-1.975l.15-.15z"/></svg>
                    </button>
                    
                    <div class="search-dropdown" id="search-dropdown">
                        <input type="text" id="live-search" placeholder="Артикул или название..." autocomplete="off">
                        <div id="search-results" class="search-results"></div>
                    </div>
                </div>

                <button class="icon-btn" aria-label="Избранное"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.001 4.529c2.349-2.109 5.979-2.039 8.242.228 2.262 2.268 2.34 5.88.236 8.236l-8.48 8.492-8.478-8.492c-2.104-2.356-2.025-5.974.236-8.236 2.265-2.264 5.888-2.34 8.244-.228z"/></svg></button>
                <button class="icon-btn" aria-label="Корзина"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg></button>
            </div>
            <button class="burger-btn" id="burger-btn" aria-label="Меню"><span></span><span></span><span></span></button>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('search-toggle');
    const dropdown = document.getElementById('search-dropdown');
    const input = document.getElementById('live-search');
    const results = document.getElementById('search-results');

    // Открытие/Закрытие по клику на лупу
    toggle.addEventListener('click', function(e) {
        e.stopPropagation(); // Чтобы клик не ушел в document
        dropdown.classList.toggle('active');
        if (dropdown.classList.contains('active')) {
            setTimeout(() => input.focus(), 100);
        }
    });

    // Закрытие при клике вне меню
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target) && e.target !== toggle) {
            dropdown.classList.remove('active');
        }
    });

    // Живой поиск
    let debounceTimer;
    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        let val = this.value.trim();
        
        if(val.length < 2) {
            results.innerHTML = '';
            return;
        }

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
                        link.innerHTML = `<span class="s-art">${item.part_number}</span><span class="s-name">${item.name}</span>`;
                        results.appendChild(link);
                    });
                }
            })
            .catch(err => console.error(err));
        }, 300);
    });
});
</script>