/**
 * Cart page — AJAX-удаление товаров, очистка корзины,
 * обновление итогов и пустое состояние.
 */
document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.cart-page');
    if (!page) return;

    /* ==========================================================
       0. Кнопки с data-confirm: «Очистить корзину»
          — confirm всегда, AJAX только для .cart-clear
       ========================================================== */
    page.addEventListener('click', (e) => {
        const clearBtn = e.target.closest('[data-confirm]');
        if (!clearBtn) return;

        const message = clearBtn.dataset.confirm;
        if (message && !window.confirm(message)) {
            e.preventDefault();
            return;
        }

        if (clearBtn.classList.contains('cart-clear')) {
            e.preventDefault();
            clearCart(clearBtn);
        }
    });

    /* ==========================================================
       1. Выбрать всё + синхронизация чекбоксов
       ========================================================== */
    const selectAll = page.querySelector('.cart-select-all__input');
    const getChecks = () => page.querySelectorAll('[data-cart-item-check]');

    function syncSelectAll() {
        if (!selectAll) return;
        const checks = [...getChecks()];
        const allOn  = checks.length > 0 && checks.every(c => c.checked);
        selectAll.checked = allOn;
    }

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            getChecks().forEach(c => { c.checked = selectAll.checked; });
        });

        page.addEventListener('change', (e) => {
            if (e.target.matches('[data-cart-item-check]')) syncSelectAll();
        });

        syncSelectAll();
    }

    /* ==========================================================
       2. AJAX — удаление одного товара и очистка корзины
       ========================================================== */
    if (typeof cart_ajax === 'undefined' || !cart_ajax.url) return;

    const shopUrl      = cart_ajax.shop_url || '/shop/';
    const cartCountEl  = document.querySelector('.cart-count');
    const summaryList  = document.querySelector('[data-cart-summary-list]');
    const summaryCount = document.querySelector('[data-cart-summary-count]');

    /**
     * Возвращает актуальный элемент .cart-summary__total-sum.
     */
    function getSummaryTotalEl() {
        return document.querySelector('[data-cart-summary-total]');
    }

    /**
     * Удаление одного товара.
     */
    async function removeItem(link) {
        const item = link.closest('.cart-item');
        const key  = item?.dataset.cartItem;

        if (!item || !key) return;

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

            animateRemove(item);

            const summaryItem = summaryList?.querySelector(`[data-cart-item="${key}"]`);
            if (summaryItem) animateRemove(summaryItem);

            updateCounters(data);
            updateTotal(data);

            setTimeout(syncSelectAll, 50);

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
     * AJAX-очистка корзины.
     *
     * @param {HTMLAnchorElement} btn
     */
    async function clearCart(btn) {
        if (btn.classList.contains('is-loading')) return;

        btn.classList.add('is-loading');
        btn.setAttribute('aria-busy', 'true');

        const body = new URLSearchParams({
            action: 'my_theme_clear_cart',
            nonce:  cart_ajax.nonce,
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
                throw new Error(json.data?.message || 'Ошибка очистки');
            }

            const data = json.data;

            updateCounters(data);
            updateTotal(data);
            showEmptyState();
        } catch (err) {
            btn.classList.remove('is-loading');
            btn.removeAttribute('aria-busy');
            console.error('[cart] clear error:', err);
            alert('Не удалось очистить корзину. Попробуйте ещё раз.');
        }
    }

    /**
     * Обновляет количество в правой колонке и в бейдже хедера.
     */
    function updateCounters(data) {
        if (summaryCount) {
            summaryCount.textContent = data.items_count;
        }

        if (cartCountEl) {
            cartCountEl.textContent = data.items_count;
            if (data.items_count === 0) {
                cartCountEl.setAttribute('hidden', '');
            } else {
                cartCountEl.removeAttribute('hidden');
            }
        }
    }

    /**
     * Обновляет блок «Итого».
     */
    function updateTotal(data) {
        const el = getSummaryTotalEl();
        if (!el) return;
        if (typeof data.total_html !== 'string') return;

        el.innerHTML = data.total_html;
    }

    /**
     * Плавно убирает элемент.
     */
    function animateRemove(el) {
        if (!el || !el.parentNode) return;

        const height = el.offsetHeight;
        el.style.height   = height + 'px';
        el.style.overflow = 'hidden';

        requestAnimationFrame(() => {
            el.classList.add('is-hidden');
            el.style.height = '0px';

            const cleanup = () => {
                if (el.parentNode) el.remove();
            };

            el.addEventListener('transitionend', cleanup, { once: true });
            setTimeout(cleanup, 500);
        });
    }

    /**
     * Показывает пустое состояние той же разметкой, что и SSR.
     */
    function showEmptyState() {
        const container = page.querySelector('.container');
        if (!container) return;

        const form = container.querySelector('.cart-form');
        if (form) form.remove();

        const oldEmpty = container.querySelector('.cart-empty');
        if (oldEmpty) oldEmpty.remove();

        const div = document.createElement('div');
        div.className = 'cart-empty';
        div.innerHTML = `
            <p>Корзина пуста.</p>
            <a href="${shopUrl}" class="btn btn-dark">В каталог</a>
        `;
        container.appendChild(div);
    }

    /* Делегируем клик по крестикам удаления */
    page.addEventListener('click', (e) => {
        const link = e.target.closest('[data-cart-item-remove]');
        if (!link) return;

        e.preventDefault();
        removeItem(link);
    });
});