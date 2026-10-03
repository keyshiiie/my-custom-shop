// ============================================
// AJAX-фильтрация каталога + AJAX-пагинация
// ============================================
(function () {
    'use strict';

    function init() {
        const resultsEl  = document.querySelector('[data-catalog-results]');
        const chipsCount = document.querySelector('.catalog-chips__count');
        if (!resultsEl) return;

        const ajaxUrl = window.catalog_ajax?.url;
        if (!ajaxUrl) {
            console.warn('catalog_ajax.url не задан');
            return;
        }

        let currentPage = 1;
        let isPending = false;

        // --------------------------------------------
        // Собрать состояние фильтров
        // --------------------------------------------
        function getFilters() {
            const categories = [];
            const tags = [];

            document.querySelectorAll(
                '.catalog-filter[data-filter="category"] .catalog-filter__option--checkbox.is-selected'
            ).forEach(function (el) {
                categories.push(el.dataset.value);
            });

            document.querySelectorAll(
                '.catalog-filter[data-filter="tag"] .catalog-tag.is-selected'
            ).forEach(function (el) {
                tags.push(el.dataset.value);
            });

            const priceEl     = document.querySelector('.catalog-filter[data-filter="price"]');
            const priceInputs = priceEl ? priceEl.querySelectorAll('.catalog-price__input') : [];
            const price_min   = priceInputs[0]?.value || '';
            const price_max   = priceInputs[1]?.value || '';
            const free_only   = priceEl?.querySelector('.catalog-price__free-input')?.checked ? 1 : 0;

            const sortEl = document.querySelector(
                '.catalog-filter[data-filter="sort"] .catalog-sort.is-selected'
            );
            const sort = sortEl?.dataset.value || '';

            const searchInput = document.querySelector('.catalog-search__input');
            const search = searchInput?.value.trim() || '';

            return {
                categories: categories.join(','),
                tags:       tags.join(','),
                price_min,
                price_max,
                free_only,
                sort,
                search,
            };
        }

        // --------------------------------------------
        // Отправить запрос
        // --------------------------------------------
        function applyFilters(page) {
            if (isPending) return;
            isPending = true;

            currentPage = page || 1;
            resultsEl.classList.add('is-loading');

            const filters = getFilters();

            const params = new URLSearchParams({
                action:     'filter_catalog',
                paged:      currentPage,
                categories: filters.categories,
                tags:       filters.tags,
                price_min:  filters.price_min,
                price_max:  filters.price_max,
                free_only:  filters.free_only,
                sort:       filters.sort,
                search:     filters.search,
            });

            fetch(ajaxUrl + '?' + params.toString())
                .then(function (res) { return res.json(); })
                .then(function (response) {
                    if (!response.success) {
                        console.error('AJAX error', response);
                        return;
                    }

                    const data = response.data;

                    renderResults(data);
                    updateUrl(filters);

                    if (chipsCount) {
                        chipsCount.textContent = 'Найдено: ' + data.found_posts;
                    }

                    document.dispatchEvent(new CustomEvent('catalog:filters-applied'));

                    if (page && page > 1) {
                        resultsEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                })
                .catch(function (err) {
                    console.error('Fetch error', err);
                })
                .finally(function () {
                    isPending = false;
                    resultsEl.classList.remove('is-loading');
                });
        }

        // --------------------------------------------
        // Отрисовать результаты
        // --------------------------------------------
        function renderResults(data) {
            let html = '';

            if (data.found_posts === 0) {
                html = '<div class="catalog-empty">По вашему запросу ничего не найдено. Попробуйте изменить фильтры.</div>';
            } else {
                // products-grid — общий класс сетки товаров из main.css.
                // Раньше класс добавлялся фильтром в functions.php,
                // теперь — прямо здесь, в момент рендера.
                html = '<ul class="products products-grid">' + data.html + '</ul>';

                if (data.pagination) {
                    html += data.pagination;
                }
            }

            resultsEl.innerHTML = html;
        }

        // --------------------------------------------
        // Делегирование клика по пагинации
        // Работает для любой пагинации — и из PHP, и после AJAX
        // --------------------------------------------
        function handlePaginationClick(e) {
            const link = e.target.closest('.woocommerce-pagination a');
            if (!link) return;
            if (!resultsEl.contains(link)) return;

            e.preventDefault();

            const href  = link.getAttribute('href');
            const match = href.match(/#page\/(\d+)/);
            const page  = match ? parseInt(match[1], 10) : 1;

            applyFilters(page);
        }

        // --------------------------------------------
        // Обновить URL
        // --------------------------------------------
        function updateUrl(filters) {
            const params = new URLSearchParams();

            if (filters.categories) params.set('categories', filters.categories);
            if (filters.tags)       params.set('tags', filters.tags);
            if (filters.price_min)  params.set('price_min', filters.price_min);
            if (filters.price_max)  params.set('price_max', filters.price_max);
            if (filters.free_only)  params.set('free_only', '1');
            if (filters.sort)       params.set('sort', filters.sort);
            if (filters.search)     params.set('search', filters.search);
            if (currentPage > 1)    params.set('paged', currentPage);

            const newUrl = window.location.pathname +
                (params.toString() ? '?' + params.toString() : '');

            history.pushState({ filters: filters }, '', newUrl);
        }

        // --------------------------------------------
        // Восстановить UI фильтров из URL.
        // Нужно для popstate (назад/вперёд в браузере) —
        // чтобы чекбоксы, теги и поля цены отражали URL,
        // а не то, что осталось в DOM от предыдущего состояния.
        // --------------------------------------------
        function restoreFiltersFromUrl(params) {
            const categories = (params.get('categories') || '').split(',').filter(Boolean);
            const tags       = (params.get('tags') || '').split(',').filter(Boolean);

            // Категории
            document.querySelectorAll(
                '.catalog-filter[data-filter="category"] .catalog-filter__option--checkbox'
            ).forEach(function (el) {
                const on = categories.includes(el.dataset.value);
                el.classList.toggle('is-selected', on);
                el.setAttribute('aria-selected', on ? 'true' : 'false');
            });

            // Теги
            document.querySelectorAll(
                '.catalog-filter[data-filter="tag"] .catalog-tag'
            ).forEach(function (el) {
                const on = tags.includes(el.dataset.value);
                el.classList.toggle('is-selected', on);
                el.setAttribute('aria-selected', on ? 'true' : 'false');
            });

            // Цена
            const priceEl = document.querySelector('.catalog-filter[data-filter="price"]');
            if (priceEl) {
                const inputs = priceEl.querySelectorAll('.catalog-price__input');
                if (inputs[0]) inputs[0].value = params.get('price_min') || '';
                if (inputs[1]) inputs[1].value = params.get('price_max') || '';

                const freeCb = priceEl.querySelector('.catalog-price__free-input');
                if (freeCb) {
                    freeCb.checked = !!params.get('free_only');
                    inputs.forEach(function (i) {
                        i.disabled = freeCb.checked;
                    });
                }
            }

            // Сортировка
            const sortValue = params.get('sort') || '';
            document.querySelectorAll(
                '.catalog-filter[data-filter="sort"] .catalog-sort'
            ).forEach(function (el) {
                const on = el.dataset.value === sortValue;
                el.classList.toggle('is-selected', on);
                el.setAttribute('aria-checked', on ? 'true' : 'false');
            });

            // Поиск
            const searchInput = document.querySelector('.catalog-search__input');
            if (searchInput) {
                searchInput.value = params.get('search') || '';
            }
        }

        // --------------------------------------------
        // Слушаем события
        // --------------------------------------------
        document.addEventListener('catalog:filter-changed', function () {
            applyFilters(1);
        });

        document.addEventListener('catalog:price-applied', function () {
            applyFilters(1);
        });

        document.addEventListener('catalog:filters-reset', function () {
            applyFilters(1);
        });

        // --------------------------------------------
        // Поиск — debounce
        // --------------------------------------------
        const searchInput = document.querySelector('.catalog-search__input');
        if (searchInput) {
            let searchTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    applyFilters(1);
                }, 500);
            });

            const form = searchInput.closest('form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    applyFilters(1);
                });
            }
        }

        // --------------------------------------------
        // Назад/вперёд в браузере.
        //
        // Раньше здесь было applyFilters(1) — терялась страница
        // и состояние фильтров. Теперь читаем URL, восстанавливаем UI
        // и передаём правильную страницу.
        // --------------------------------------------
        window.addEventListener('popstate', function () {
            const params = new URLSearchParams(window.location.search);
            const paged  = parseInt(params.get('paged') || '1', 10);

            restoreFiltersFromUrl(params);
            applyFilters(paged);
        });

        // --------------------------------------------
        // Единожды навешиваем делегированный обработчик
        // на клики по пагинации (и для PHP-пагинации, и для AJAX-пагинации).
        // Работает без переинициализации после каждого AJAX-запроса.
        // --------------------------------------------
        resultsEl.addEventListener('click', handlePaginationClick);
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