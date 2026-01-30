document.addEventListener('DOMContentLoaded', () => {
    updateCartHeader();

    // Открытие/закрытие мини-корзины
    const cartBtn = document.querySelector('.header-cart-btn');
    const cartDropdown = document.querySelector('.cart-dropdown');
    
    if (cartBtn && cartDropdown) {
        cartBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            cartDropdown.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!cartDropdown.contains(e.target) && !cartBtn.contains(e.target)) {
                cartDropdown.classList.remove('active');
            }
        });
    }
});

// Функция добавления в корзину
function addToCart(article) {
    const formData = new FormData();
    formData.append('action', 'add_cart');
    formData.append('art', article);

    fetch('api_actions.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        updateCartHeader();
        alert('Товар добавлен в корзину!'); // Можно заменить на красивое уведомление
    });
}

// Функция добавления в избранное
function toggleFav(article, btnElement) {
    const formData = new FormData();
    formData.append('action', 'toggle_fav');
    formData.append('art', article);

    fetch('api_actions.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if(btnElement) {
            btnElement.classList.toggle('active');
            btnElement.style.color = data.status === 'added' ? '#ff3333' : '#fff';
        }
    });
}

// Обновление шапки (циферка и список)
function updateCartHeader() {
    const formData = new FormData();
    formData.append('action', 'get_cart');

    fetch('api_actions.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        // Обновляем счетчик
        const badges = document.querySelectorAll('.cart-badge');
        badges.forEach(b => {
            b.innerText = data.count;
            b.style.display = data.count > 0 ? 'flex' : 'none';
        });

        // Обновляем выпадающий список
        const listContainer = document.querySelector('.cart-dropdown-list');
        if (listContainer) {
            listContainer.innerHTML = '';
            if (data.items.length === 0) {
                listContainer.innerHTML = '<div style="padding:15px; text-align:center; color:#666">Корзина пуста</div>';
            } else {
                data.items.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'mini-cart-item';
                    div.innerHTML = `
                        <span class="mc-art">${item.part_number}</span>
                        <span class="mc-name">${item.name || 'Деталь'}</span>
                        <span class="mc-qty">x${item.quantity}</span>
                    `;
                    listContainer.appendChild(div);
                });
            }
        }
    });
}