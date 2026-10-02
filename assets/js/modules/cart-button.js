// ============================================
// Кнопка "В корзину" → "К корзине"
// + защита от повторного добавления
// ============================================
(function () {
    'use strict';

    // Проверяет, есть ли товар в корзине
    function isInCart(productId) {
        if (!window.wc_cart_data || !window.wc_cart_data.items) return false;
        return window.wc_cart_data.items.includes(parseInt(productId, 10));
    }

    // Меняет состояние кнопки
    function setCartBtnState(btn, inCart) {
        if (inCart) {
            btn.textContent = 'К корзине';
            btn.href = btn.dataset.cart_url;
            btn.classList.remove('ajax_add_to_cart');
            btn.classList.add('is-in-cart');
        } else {
            btn.textContent = 'В корзину';
            btn.classList.add('ajax_add_to_cart');
            btn.classList.remove('is-in-cart');
        }
    }

    function init() {
        // 1. При загрузке: если товар уже в корзине — меняем кнопку
        document.querySelectorAll('.product-card__cart-btn').forEach(function (btn) {
            const productId = btn.dataset.product_id;
            if (isInCart(productId)) {
                setCartBtnState(btn, true);
            }
        });

        // 2. Клик по кнопке
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.product-card__cart-btn');
            if (!btn) return;

            const productId = btn.dataset.product_id;

            // Если товар уже в корзине — ведём в корзину
            if (isInCart(productId)) {
                e.preventDefault();
                window.location.href = btn.dataset.cart_url;
            }
            // Иначе WooCommerce сам обработает AJAX
        });

        // 3. WooCommerce AJAX: успешное добавление
        if (window.jQuery) {
            window.jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
                if (!$button || !$button.length) return;

                const productId = $button.data('product_id');
                const ourBtn = document.querySelector(
                    '.product-card__cart-btn[data-product_id="' + productId + '"]'
                );

                if (ourBtn) {
                    setCartBtnState(ourBtn, true);
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();