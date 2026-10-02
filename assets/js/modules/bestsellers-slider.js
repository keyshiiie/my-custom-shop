// ============================================
// Слайдер "Хиты продаж"
// ============================================
(function () {
    'use strict';

    function init() {
        const wraps = document.querySelectorAll('.bestsellers-wrap');

        wraps.forEach(function (wrap) {
            const slider     = wrap.querySelector('.bestsellers-slider');
            const track      = wrap.querySelector('.bestsellers-track');
            if (!slider || !track) return;

            const items      = track.querySelectorAll('.bestsellers-item');
            const prevBtn    = wrap.querySelector('.slider-arrow--prev');
            const nextBtn    = wrap.querySelector('.slider-arrow--next');
            const pagination = wrap.querySelector('.slider-pagination');

            if (!items.length) return;

            // Настройки из data-атрибутов
            const perView = parseInt(slider.dataset.perView, 10) || 3;
            const gap     = parseInt(slider.dataset.gap, 10) || 30;

            let currentPage = 0;
            const totalPages = Math.ceil(items.length / perView);

            // ----------------------------------------
            // Ширина одной карточки
            // ----------------------------------------
            function getItemWidth() {
                const trackWidth = slider.offsetWidth;
                return (trackWidth - (perView - 1) * gap) / perView;
            }

            // ----------------------------------------
            // Применяем ширину к карточкам
            // ----------------------------------------
            function applySizes() {
                const itemWidth = getItemWidth();
                items.forEach(function (item) {
                    item.style.flex = '0 0 ' + itemWidth + 'px';
                    item.style.maxWidth = itemWidth + 'px';
                });
            }

            // ----------------------------------------
            // Сдвиг трека на нужную страницу
            // ----------------------------------------
            function goToPage(page) {
                if (page < 0) page = 0;
                if (page > totalPages - 1) page = totalPages - 1;
                currentPage = page;

                const itemWidth = getItemWidth();
                const offset = -(itemWidth + gap) * perView * currentPage;
                track.style.transform = 'translateX(' + offset + 'px)';

                // Активная точка
                if (pagination) {
                    const dots = pagination.querySelectorAll('.slider-dot');
                    dots.forEach(function (dot, index) {
                        dot.classList.toggle('is-active', index === currentPage);
                    });
                }

                // Блокируем стрелки
                if (prevBtn) prevBtn.disabled = currentPage === 0;
                if (nextBtn) nextBtn.disabled = currentPage === totalPages - 1;
            }

            // ----------------------------------------
            // Точки пагинации
            // ----------------------------------------
            function buildPagination() {
                if (!pagination) return;
                pagination.innerHTML = '';

                if (totalPages <= 1) return;

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

            // ----------------------------------------
            // Инициализация
            // ----------------------------------------
            applySizes();
            buildPagination();
            goToPage(0);

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
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();