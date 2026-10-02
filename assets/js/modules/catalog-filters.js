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
        // Инициализация
        // --------------------------------------------
        filters.forEach(function (filter) {
            const toggle  = filter.querySelector('.catalog-filter__toggle');
            const label   = filter.querySelector('.catalog-filter__label');
            const isMulti = filter.classList.contains('catalog-filter--multiselect');
            const isRadio = filter.classList.contains('catalog-filter--sort');

            if (!toggle || !label) return;

            const placeholder = filter.dataset.placeholder || label.textContent.trim();

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
            // Кликабельные элементы внутри фильтра
            // --------------------------------------------
            const clickables = filter.querySelectorAll(
                '.catalog-filter__option, .catalog-tag, .catalog-sort'
            );

            clickables.forEach(function (option) {
                option.addEventListener('click', function () {
                    // НЕ вызываем stopPropagation —
                    // пусть событие всплывёт, чтобы catalog-chips.js узнал об изменении

                    // ----------------------------------------
                    // Мультивыбор (чекбоксы / чипсы)
                    // ----------------------------------------
                    if (isMulti) {
                        option.classList.toggle('is-selected');
                        const selected = option.classList.contains('is-selected');
                        option.setAttribute('aria-selected', selected ? 'true' : 'false');
                        return;
                    }

                    // ----------------------------------------
                    // Радио-сортировка
                    // ----------------------------------------
                    if (isRadio) {
                        clickables.forEach(function (o) {
                            o.classList.remove('is-selected');
                            o.setAttribute('aria-checked', 'false');
                        });
                        option.classList.add('is-selected');
                        option.setAttribute('aria-checked', 'true');
                        return;
                    }

                    // ----------------------------------------
                    // Обычный одиночный выбор (fallback)
                    // ----------------------------------------
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
                });
            });

            // --------------------------------------------
            // Кнопка «Применить» в панели цены
            // --------------------------------------------
            const applyBtn = filter.querySelector('.catalog-price__apply');
            if (applyBtn) {
                applyBtn.addEventListener('click', function (e) {
                    e.stopPropagation();

                    const inputs   = filter.querySelectorAll('.catalog-price__input');
                    const min      = inputs[0] ? inputs[0].value : '';
                    const max      = inputs[1] ? inputs[1].value : '';
                    const freeOnly = !!filter.querySelector('.catalog-price__free-input')?.checked;

                    filter.classList.remove('is-open');
                    toggle.setAttribute('aria-expanded', 'false');

                    console.log('Цена:', { min, max, freeOnly });
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