// ============================================
// Слайдер
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    const wraps = document.querySelectorAll('.bestsellers-wrap');

    wraps.forEach(function (wrap) {
        const slider      = wrap.querySelector('.bestsellers-slider');
        const track       = wrap.querySelector('.bestsellers-track');
        const items       = track.querySelectorAll('.bestsellers-item');
        const prevBtn     = wrap.querySelector('.slider-arrow--prev');
        const nextBtn     = wrap.querySelector('.slider-arrow--next');
        const pagination  = wrap.querySelector('.slider-pagination');

        if (!items.length || !slider) return;

        // Читаем настройки из data-атрибутов
        const perView = parseInt(slider.dataset.perView, 10) || 3;
        const gap     = parseInt(slider.dataset.gap, 10) || 30;

        let currentPage = 0;
        const totalPages = Math.ceil(items.length / perView);

        // ============================================
        // Расчёт ширины одной карточки
        // ============================================
        function getItemWidth() {
            const trackWidth = slider.offsetWidth;
            return (trackWidth - (perView - 1) * gap) / perView;
        }

        // ============================================
        // Применяем ширину к карточкам
        // ============================================
        function applySizes() {
            const itemWidth = getItemWidth();
            items.forEach(function (item) {
                item.style.flex = '0 0 ' + itemWidth + 'px';
                item.style.maxWidth = itemWidth + 'px';
            });
        }

        // ============================================
        // Сдвиг трека на нужную страницу
        // ============================================
        function goToPage(page) {
            if (page < 0) page = 0;
            if (page > totalPages - 1) page = totalPages - 1;
            currentPage = page;

            const itemWidth = getItemWidth();
            const offset = -(itemWidth + gap) * perView * currentPage;
            track.style.transform = 'translateX(' + offset + 'px)';

            // Обновляем активную точку
            const dots = pagination.querySelectorAll('.slider-dot');
            dots.forEach(function (dot, index) {
                dot.classList.toggle('is-active', index === currentPage);
            });

            // Блокируем стрелки
            if (prevBtn) prevBtn.disabled = currentPage === 0;
            if (nextBtn) nextBtn.disabled = currentPage === totalPages - 1;
        }

        // ============================================
        // Строим точки пагинации
        // ============================================
        function buildPagination() {
            if (!pagination) return;
            pagination.innerHTML = '';

            if (totalPages <= 1) return; // если страница одна — не нужна

            for (let i = 0; i < totalPages; i++) {
                const dot = document.createElement('button');
                dot.className = 'slider-dot' + (i === 0 ? ' is-active' : '');
                dot.setAttribute('aria-label', 'Страница ' + (i + 1));
                dot.addEventListener('click', function () {
                    goToPage(i);
                });
                pagination.appendChild(dot);
            }
        }

        // ============================================
        // Инициализация
        // ============================================
        applySizes();
        buildPagination();
        goToPage(0);

        // Стрелки
        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                goToPage(currentPage - 1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                goToPage(currentPage + 1);
            });
        }

        // Пересчёт при ресайзе
        let resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                applySizes();
                goToPage(currentPage);
            }, 100);
        });
    });
});

// ============================================
// FAQ: плавное открытие/закрытие
// ============================================
document.querySelectorAll('.faq-item').forEach(function (item) {
    const answer   = item.querySelector('.faq-item__answer');
    const question = item.querySelector('.faq-item__question');
    if (!answer || !question) return;

    // Если изначально открыт — снимаем ограничение высоты
    if (item.open) {
        answer.style.maxHeight = 'none';
    }

    question.addEventListener('click', function (e) {
        e.preventDefault();

        if (item.open) {
            closeFaq(item, answer);
        } else {
            // Закрываем все остальные открытые
            document.querySelectorAll('.faq-item[open]').forEach(function (other) {
                if (other !== item) {
                    const otherAnswer = other.querySelector('.faq-item__answer');
                    closeFaq(other, otherAnswer);
                }
            });
            openFaq(item, answer);
        }
    });
});

// ============================================
// Открытие
// ============================================
function openFaq(item, answer) {
    item.open = true;

    // 1. Фиксируем текущую высоту (0) — мгновенно, без анимации
    answer.style.transition = 'none';
    answer.style.maxHeight = '0px';

    // 2. Форсируем reflow
    void answer.offsetHeight;

    // 3. Возвращаем transition и ставим целевую высоту
    requestAnimationFrame(function () {
        answer.style.transition = '';
        answer.style.maxHeight = answer.scrollHeight + 'px';
    });

    // 4. После завершения анимации снимаем ограничение (чтобы контент мог расти)
    answer.addEventListener('transitionend', function handler(e) {
        if (e.propertyName !== 'max-height') return;
        answer.style.maxHeight = 'none';
        answer.removeEventListener('transitionend', handler);
    });
}

// ============================================
// Закрытие — самое надёжное
// ============================================
function closeFaq(item, answer) {
    // 1. Фиксируем текущую высоту (если max-height: none — берём scrollHeight)
    answer.style.transition = 'none';
    answer.style.maxHeight = answer.scrollHeight + 'px';

    // 2. Форсируем reflow — критично для плавного закрытия
    void answer.offsetHeight;

    // 3. Включаем transition и анимируем до 0
    requestAnimationFrame(function () {
        answer.style.transition = '';
        answer.style.maxHeight = '0px';
    });

    // 4. Ждём завершения анимации
    let closed = false;
    const finish = function () {
        if (closed) return;
        closed = true;
        item.open = false;
        answer.removeEventListener('transitionend', onEnd);
        clearTimeout(fallbackTimer);
    };
    const onEnd = function (e) {
        if (e.propertyName !== 'max-height') return;
        finish();
    };
    answer.addEventListener('transitionend', onEnd);

    // 5. Fallback: если transitionend не сработал — закрываем принудительно через 500ms
    const fallbackTimer = setTimeout(finish, 500);
}

// ============================================
// АНИМАЦИЯ СМЕНЫ СЛОВА В HERO HIGHLIGHT
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    const highlightEl = document.querySelector('.hero-title .highlight');
    const wordEl      = document.querySelector('.hero-title .highlight__word');
    if (!highlightEl || !wordEl) return;

    const words = ['время', 'деньги', 'силы', 'ресурсы'];
    let currentIndex = 0;

    const INTERVAL     = 2800;   // 2.8s между сменами
    const ANIM_DURATION = 500;   // 0.5s — совпадает с transition
    const ENTER_DELAY  = 20;     // 20ms для сброса transition

    function changeWord() {
        // 1. Уходим: класс на обёртку (плашка + слово)
        highlightEl.classList.add('is-leaving');

        setTimeout(function () {
            // 2. Меняем текст
            currentIndex = (currentIndex + 1) % words.length;
            wordEl.textContent = words[currentIndex];

            // 3. Меняем классы: убираем is-leaving, добавляем is-entering
            highlightEl.classList.remove('is-leaving');
            highlightEl.classList.add('is-entering');

            // 4. Через 20ms убираем is-entering — плавно приходим в норму
            setTimeout(function () {
                highlightEl.classList.remove('is-entering');
            }, ENTER_DELAY);
        }, ANIM_DURATION);
    }

    setInterval(changeWord, INTERVAL);
});

// ============================================
// КНОПКА "В КОРЗИНУ" → "ПОСМОТРЕТЬ КОРЗИНУ"
// И защита от повторного добавления
// ============================================
document.addEventListener('DOMContentLoaded', function () {

    // ============================================
    // 1. При загрузке: если товар уже в корзине — меняем кнопку
    // ============================================
    document.querySelectorAll('.product-card__cart-btn').forEach(function (btn) {
        const productId = btn.dataset.product_id;
        if (isInCart(productId)) {
            setCartBtnState(btn, true);
        }
    });

    // ============================================
    // 2. Клик по кнопке "В корзину"
    // ============================================
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.product-card__cart-btn');
        if (!btn) return;

        const productId = btn.dataset.product_id;

        // Если товар уже в корзине — просто ведём в корзину
        if (isInCart(productId)) {
            e.preventDefault();
            window.location.href = btn.dataset.cart_url;
            return;
        }

        // Если нет — WooCommerce сам обработает AJAX.
        // Но после успешного добавления — переключим состояние кнопки.
    });

    // ============================================
    // 3. WooCommerce AJAX: успешное добавление
    // ============================================
    if (window.jQuery) {
        jQuery(document.body).on('added_to_cart', function (event, fragments, cart_hash, $button) {
            if (!$button || !$button.length) return;
            const productId = $button.data('product_id');
            const $ourBtn = document.querySelector('.product-card__cart-btn[data-product_id="' + productId + '"]');
            if ($ourBtn) {
                setCartBtnState($ourBtn, true);
            }
        });
    }

    // ============================================
    // Вспомогательные функции
    // ============================================

    // Проверяет, есть ли товар в корзине
    function isInCart(productId) {
        if (!window.wc_cart_data || !window.wc_cart_data.items) return false;
        return window.wc_cart_data.items.includes(parseInt(productId, 10));
    }

    // Меняет состояние кнопки: "В корзину" ↔ "Посмотреть корзину"
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
});