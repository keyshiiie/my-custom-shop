// ============================================
// Активные чипсы фильтров
// ============================================
(function () {
    'use strict';

    // Максимум видимых чипсов до сворачивания в «+N»
    const MAX_VISIBLE = 3;

    function init() {
        const chipsContainer = document.querySelector('.catalog-chips');
        if (!chipsContainer) return;

        const listEl       = chipsContainer.querySelector('[data-chips-list]');
        const moreBtn      = chipsContainer.querySelector('[data-chips-more]');
        const moreCountEl  = chipsContainer.querySelector('[data-chips-more-count]');
        const resetBtn     = chipsContainer.querySelector('[data-chips-reset]');

        if (!listEl) return;

        // --------------------------------------------
        // Состояние: массив { type, value, label }
        // --------------------------------------------
        let activeFilters = [];

        // --------------------------------------------
        // Собрать активные фильтры из DOM
        // --------------------------------------------
        function collectActive() {
            const result = [];

            // Категории (чекбоксы)
            document.querySelectorAll(
                '.catalog-filter[data-filter="category"] .is-selected'
            ).forEach(function (el) {
                result.push({
                    type: 'category',
                    value: el.dataset.value,
                    label: el.querySelector('.catalog-filter__option-text')?.textContent.trim() || el.dataset.value,
                });
            });

            // Теги (чипсы)
            document.querySelectorAll(
                '.catalog-filter[data-filter="tag"] .catalog-tag.is-selected'
            ).forEach(function (el) {
                const text = el.textContent.trim(); // уже с #
                result.push({
                    type: 'tag',
                    value: el.dataset.value,
                    label: text,
                });
            });

            // Сортировка (radio)
            document.querySelectorAll(
                '.catalog-filter[data-filter="sort"] .catalog-sort.is-selected'
            ).forEach(function (el) {
                const text = el.querySelector('.catalog-sort__text')?.textContent.trim() || '';
                result.push({
                    type: 'sort',
                    value: el.dataset.value,
                    label: text,
                });
            });

            // Цена (панель)
            const priceFilter = document.querySelector('.catalog-filter[data-filter="price"]');
            if (priceFilter) {
                const min  = priceFilter.querySelectorAll('.catalog-price__input')[0]?.value || '';
                const max  = priceFilter.querySelectorAll('.catalog-price__input')[1]?.value || '';
                const free = priceFilter.querySelector('.catalog-price__free-input')?.checked || false;

                if (min) result.push({ type: 'price-min', value: min, label: 'от ' + min + '₽' });
                if (max) result.push({ type: 'price-max', value: max, label: 'до ' + max + '₽' });
                if (free) result.push({ type: 'price-free', value: '1', label: 'Бесплатные' });
            }

            activeFilters = result;
            return result;
        }

        // --------------------------------------------
        // Отрисовать чипсы
        // --------------------------------------------
        let isExpanded = false;

        function renderChips() {
            const items = activeFilters;
            listEl.innerHTML = '';

            if (!items.length) {
                moreBtn.hidden = true;
                if (resetBtn) resetBtn.hidden = true;
                return;
            }

            if (resetBtn) resetBtn.hidden = false;

            // Сколько показываем
            const visibleCount = isExpanded ? items.length : MAX_VISIBLE;
            const visible = items.slice(0, visibleCount);
            const hiddenCount = items.length - visibleCount;

            visible.forEach(function (item) {
                const chip = document.createElement('span');
                chip.className = 'catalog-chip';
                if (item.type === 'tag') chip.classList.add('catalog-chip--tag');
                chip.dataset.type = item.type;
                chip.dataset.value = item.value;

                chip.innerHTML =
                    '<span class="catalog-chip__label">' + escapeHtml(item.label) + '</span>' +
                    '<button type="button" class="catalog-chip__close" aria-label="Убрать фильтр">' +
                        '<svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                            '<path d="M9 3L3 9M3 3L9 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
                        '</svg>' +
                    '</button>';

                // Клик по крестику — снять фильтр
                chip.querySelector('.catalog-chip__close').addEventListener('click', function (e) {
                    e.stopPropagation();
                    removeFilter(item);
                });

                listEl.appendChild(chip);
            });

            // Плашка «+N»
            if (!isExpanded && hiddenCount > 0) {
                moreBtn.hidden = false;
                moreCountEl.textContent = '+' + hiddenCount;
                moreBtn.setAttribute('aria-label', 'Показать ещё ' + hiddenCount + ' фильтров');
            } else if (isExpanded && items.length > MAX_VISIBLE) {
                // В раскрытом состоянии — можно свернуть обратно
                moreBtn.hidden = false;
                moreCountEl.textContent = '−';
                moreBtn.setAttribute('aria-label', 'Свернуть');
            } else {
                moreBtn.hidden = true;
            }
        }

        // --------------------------------------------
        // Снять фильтр
        // --------------------------------------------
        function removeFilter(item) {
            if (item.type === 'category') {
                const el = document.querySelector(
                    '.catalog-filter[data-filter="category"] .is-selected[data-value="' + item.value + '"]'
                );
                if (el) {
                    el.classList.remove('is-selected');
                    el.setAttribute('aria-selected', 'false');
                }
            } else if (item.type === 'tag') {
                const el = document.querySelector(
                    '.catalog-filter[data-filter="tag"] .catalog-tag.is-selected[data-value="' + item.value + '"]'
                );
                if (el) {
                    el.classList.remove('is-selected');
                    el.setAttribute('aria-selected', 'false');
                }
            } else if (item.type === 'sort') {
                document.querySelectorAll(
                    '.catalog-filter[data-filter="sort"] .catalog-sort.is-selected'
                ).forEach(function (el) {
                    el.classList.remove('is-selected');
                    el.setAttribute('aria-checked', 'false');
                });
            } else if (item.type === 'price-min') {
                const input = document.querySelector('.catalog-filter[data-filter="price"] .catalog-price__input');
                if (input) input.value = '';
            } else if (item.type === 'price-max') {
                const inputs = document.querySelectorAll('.catalog-filter[data-filter="price"] .catalog-price__input');
                if (inputs[1]) inputs[1].value = '';
            } else if (item.type === 'price-free') {
                const cb = document.querySelector('.catalog-filter[data-filter="price"] .catalog-price__free-input');
                if (cb) cb.checked = false;
            }

            refresh();
        }

        // --------------------------------------------
        // Обновить состояние
        // --------------------------------------------
        function refresh() {
            collectActive();
            renderChips();
        }

        // Показать/скрыть всё
        if (moreBtn) {
            moreBtn.addEventListener('click', function (e) {
                e.preventDefault();
                isExpanded = !isExpanded;
                renderChips();
            });
        }

        // «Сбросить всё»
        if (resetBtn) {
            resetBtn.addEventListener('click', function (e) {
                e.preventDefault();

                // Снять все чекбоксы категорий
                document.querySelectorAll(
                    '.catalog-filter[data-filter="category"] .is-selected'
                ).forEach(function (el) {
                    el.classList.remove('is-selected');
                    el.setAttribute('aria-selected', 'false');
                });

                // Снять все чипсы тегов
                document.querySelectorAll(
                    '.catalog-filter[data-filter="tag"] .catalog-tag.is-selected'
                ).forEach(function (el) {
                    el.classList.remove('is-selected');
                    el.setAttribute('aria-selected', 'false');
                });

                // Снять радио сортировки
                document.querySelectorAll(
                    '.catalog-filter[data-filter="sort"] .catalog-sort.is-selected'
                ).forEach(function (el) {
                    el.classList.remove('is-selected');
                    el.setAttribute('aria-checked', 'false');
                });

                // Очистить поля цены
                document.querySelectorAll(
                    '.catalog-filter[data-filter="price"] .catalog-price__input'
                ).forEach(function (el) { el.value = ''; });
                const freeCb = document.querySelector(
                    '.catalog-filter[data-filter="price"] .catalog-price__free-input'
                );
                if (freeCb) freeCb.checked = false;

                isExpanded = false;
                refresh();
            });
        }

        // --------------------------------------------
        // Слушаем изменения в фильтрах
        // --------------------------------------------
        // Клики по чекбоксам, тегам, радиокнопкам
        document.addEventListener('click', function (e) {
            if (e.target.closest('.catalog-filter')) {
                // Дадим другим обработчикам сначала отработать
                setTimeout(refresh, 0);
            }
        });

        // Ввод в полях цены и переключение чекбокса «Бесплатные»
        document.addEventListener('change', function (e) {
            if (e.target.closest('.catalog-filter[data-filter="price"]')) {
                refresh();
            }
        });

        // Кнопка «Применить» в цене
        document.addEventListener('click', function (e) {
            if (e.target.closest('.catalog-price__apply')) {
                setTimeout(refresh, 0);
            }
        });

        // --------------------------------------------
        // Утилита — экранирование HTML
        // --------------------------------------------
        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // --------------------------------------------
        // Первый рендер
        // --------------------------------------------
        refresh();
    }

    // --------------------------------------------
    // Запуск
    // --------------------------------------------
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();