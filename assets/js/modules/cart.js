document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.cart-page');
    if (!page) return;

    /* ==========================================================
       1. Выбрать всё + синхронизация чекбоксов
       ========================================================== */
    const selectAll  = page.querySelector('.cart-select-all__input');
    const itemChecks = () => page.querySelectorAll('[data-cart-item-check]');

    if (selectAll) {
        const sync = () => {
            const checks = [...itemChecks()];
            const allOn  = checks.length && checks.every(c => c.checked);
            selectAll.checked = allOn;
        };

        selectAll.addEventListener('change', () => {
            itemChecks().forEach(c => { c.checked = selectAll.checked; });
        });

        page.addEventListener('change', (e) => {
            if (e.target.matches('[data-cart-item-check]')) sync();
        });

        sync();
    }

    /* ==========================================================
       2. AJAX-удаление товара
       ========================================================== */
    if (typeof cart_ajax === 'undefined') return;

    const cartCountEl  = document.querySelector('.cart-count');
    const summaryList  = document.querySelector('[data-cart-summary-list]');
    const summaryTotal = document.querySelector('[data-cart-summary-total]');
    const summaryCount = document.querySelector('[data-cart-summary-count]');
    const checkoutBtn  = document.querySelector('[data-cart-checkout]');

    /**
     * Анимация удаления + собственно удаление
     */
    async function removeItem(link) {
        const item  = link.closest('.cart-item');
        const key   = item?.dataset.cartItem;

        if (!item || !key) return;

        // Показываем загрузку
        item.classList.add('is-removing');

        const body = new URLSearchParams({
            action:        'my_theme_remove_cart_item',
            nonce:         cart_ajax.nonce,
            cart_item_key: key,
        });

        try {
            const response = await fetch(cart_ajax.url, {
                method:      'POST',
                credentials: 'same-origin',
                headers:     { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                body:        body.toString(),
            });

            const json = await response.json();

            if (!json.success) {
                throw new Error(json.data?.message || 'Ошибка удаления');
            }

            const data = json.data;

            // --- 1. Анимируем и удаляем карточку товара ---
            animateRemove(item);

            // --- 2. Удаляем строку из «Ваш заказ» ---
            const summaryItem = summaryList?.querySelector(`[data-cart-item="${key}"]`);
            if (summaryItem) animateRemove(summaryItem);

            // --- 3. Обновляем цифры ---
            if (summaryTotal) summaryTotal.innerHTML = data.total_html;
            if (summaryCount) summaryCount.textContent = data.items_count;
            if (cartCountEl)  cartCountEl.textContent  = data.items_count;

            // --- 4. Если корзина пуста — показываем пустое состояние ---
            if (data.is_empty) {
                showEmptyState();
            }
        } catch (err) {
            item.classList.remove('is-removing');
            console.error('[cart] remove error:', err);
            alert('Не удалось удалить товар. Попробуйте ещё раз.');
        }
    }

    /**
     * Плавно убирает элемент: сначала opacity 0, потом height 0.
     */
    function animateRemove(el) {
        el.style.height = el.offsetHeight + 'px';
        requestAnimationFrame(() => {
            el.classList.add('is-hidden');
            el.style.height = '0px';
            el.addEventListener('transitionend', () => el.remove(), { once: true });
            // на всякий случай — страховка, если transitionend не сработал
            setTimeout(() => el.remove(), 500);
        });
    }

    /**
     * Что показывать, когда корзина опустела.
     */
    function showEmptyState() {
        // Скрываем левую и правую колонки
        document.querySelector('.cart-layout')?.remove();

        // Показываем сообщение
        const container = page.querySelector('.container');
        if (container && !page.querySelector('.cart-empty')) {
            const div = document.createElement('div');
            div.className = 'cart-empty reveal reveal--fade';
            div.innerHTML = `
                <p>Корзина пуста.</p>
                <a href="/shop/" class="btn btn-dark">В каталог</a>
            `;
            container.appendChild(div);
        }
    }

    /* Делегируем клик по всем крестикам */
    page.addEventListener('click', (e) => {
        const link = e.target.closest('[data-cart-item-remove]');
        if (!link) return;

        e.preventDefault();
        removeItem(link);
    });
});