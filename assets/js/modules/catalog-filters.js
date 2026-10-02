// ============================================
// Кастомные дропдауны в каталоге
// ============================================
(function () {
    'use strict';

    function init() {
        const filters = document.querySelectorAll('.catalog-filter');
        if (!filters.length) return;

        // --------------------------------------------
        // Закрыть все, кроме указанного
        // --------------------------------------------
        function closeAll(except) {
            filters.forEach(function (f) {
                if (f === except) return;
                f.classList.remove('is-open');
                const t = f.querySelector('.catalog-filter__toggle');
                if (t) t.setAttribute('aria-expanded', 'false');
            });
        }

        // --------------------------------------------
        // Инициализация каждого фильтра
        // --------------------------------------------
        filters.forEach(function (filter) {
            const toggle  = filter.querySelector('.catalog-filter__toggle');
            const label   = filter.querySelector('.catalog-filter__label');
            const isMulti = filter.classList.contains('catalog-filter--multiselect');
            const isRadio = filter.classList.contains('catalog-filter--sort');

            if (!toggle || !label) return;

            const placeholder = filter.dataset.placeholder || label.textContent.trim();

            // --------------------------------------------
            // Панель цены: чекбокс «Только бесплатные» ↔ поля «От/До»
            // --------------------------------------------
            const priceFreeInput = filter.querySelector('.catalog-price__free-input');
            const priceInputs    = filter.querySelectorAll('.catalog-price__input');

            if (priceFreeInput && priceInputs.length) {
                // Клик по чекбоксу «Только бесплатные»
                priceFreeInput.addEventListener('change', function () {
                    if (priceFreeInput.checked) {
                        // Очищаем поля и блокируем
                        priceInputs.forEach(function (input) {
                            input.value = '';
                            input.disabled = true;
                        });
                    } else {
                        // Разблокируем
                        priceInputs.forEach(function (input) {
                            input.disabled = false;
                        });
                    }
                });

                // Ввод в полях «От/До» — снимает чекбокс
                priceInputs.forEach(function (input) {
                    input.addEventListener('input', function () {
                        if (input.value !== '' && priceFreeInput.checked) {
                            priceFreeInput.checked = false;
                            priceInputs.forEach(function (i) {
                                i.disabled = false;
                            });
                        }
                    });
                });
            }
            // --------------------------------------------
            // Открытие / закрытие дропдауна
            // --------------------------------------------
            toggle.addEventListener('click', function (e) {
                e.stopPropagation();

                const willOpen = !filter.classList.contains('is-open');
                closeAll(filter);

                if (willOpen) {
                    filter.classList.add('is-open');
                    toggle.setAttribute('aria-expanded', 'true');
                } else {
                    filter.classList.remove('is-open');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });

            // --------------------------------------------
            // Кликабельные элементы внутри
            // --------------------------------------------
            const clickables = filter.querySelectorAll(
                '.catalog-filter__option, .catalog-tag, .catalog-sort'
            );

            clickables.forEach(function (option) {
                option.addEventListener('click', function () {
                    // ----------- Мультивыбор -----------
                    if (isMulti) {
                        option.classList.toggle('is-selected');
                        const selected = option.classList.contains('is-selected');
                        option.setAttribute('aria-selected', selected ? 'true' : 'false');

                        document.dispatchEvent(new CustomEvent('catalog:filter-changed'));
                        return;
                    }

                    // ----------- Радио (сортировка) -----------
                    if (isRadio) {
                        clickables.forEach(function (o) {
                            o.classList.remove('is-selected');
                            o.setAttribute('aria-checked', 'false');
                        });
                        option.classList.add('is-selected');
                        option.setAttribute('aria-checked', 'true');

                        document.dispatchEvent(new CustomEvent('catalog:filter-changed'));
                        return;
                    }

                    // ----------- Обычный одиночный выбор -----------
                    const value = option.dataset.value;
                    const text  = option.textContent.trim();

                    label.textContent = value ? text : placeholder;

                    clickables.forEach(function (o) {
                        o.classList.remove('is-selected');
                        o.setAttribute('aria-selected', 'false');
                    });
                    option.classList.add('is-selected');
                    option.setAttribute('aria-selected', 'true');

                    filter.classList.remove('is-open');
                    toggle.setAttribute('aria-expanded', 'false');

                    document.dispatchEvent(new CustomEvent('catalog:filter-changed'));
                });
            });

            // --------------------------------------------
            // Кнопка «Применить» в панели цены
            // --------------------------------------------
            const applyBtn = filter.querySelector('.catalog-price__apply');
            if (applyBtn) {
                applyBtn.addEventListener('click', function (e) {
                    e.stopPropagation();

                    filter.classList.remove('is-open');
                    toggle.setAttribute('aria-expanded', 'false');

                    document.dispatchEvent(new CustomEvent('catalog:price-applied'));
                });
            }
        });

        // --------------------------------------------
        // Клик вне фильтра — закрыть всё
        // --------------------------------------------
        document.addEventListener('click', function (e) {
            if (e.target.closest('.catalog-filter')) return;
            closeAll(null);
        });

        // Esc — закрыть всё
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAll(null);
            }
        });
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