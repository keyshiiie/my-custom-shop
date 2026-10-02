// ============================================
// Подсветка активного пункта меню
//
// На главной: следим за скроллом, подсвечиваем якоря.
// В каталоге: подсвечиваем пункт "Каталог".
// ============================================
(function () {
    'use strict';

    function init() {
        const menu = document.querySelector('.nav-menu');
        if (!menu) return;

        const allLinks = menu.querySelectorAll('a');
        if (!allLinks.length) return;

        // --------------------------------------------
        // РЕЖИМ 1: КАТАЛОГ — подсветить пункт "Каталог"
        // --------------------------------------------
        if (document.body.classList.contains('post-type-archive-product')
            || document.body.classList.contains('tax-product_cat')
            || document.body.classList.contains('tax-product_tag')
            || document.body.classList.contains('single-product')
            || document.body.classList.contains('woocommerce-shop')
        ) {
            allLinks.forEach(function (link) {
                const href = link.getAttribute('href');
                if (!href) return;

                // Ищем пункт, ведущий на /shop/ или /catalog/ (страница WC)
                const url = new URL(link.href, window.location.origin);
                const path = url.pathname.replace(/\/$/, ''); // убираем хвостовой /

                if (path === '/shop' || path === '/catalog' || path.endsWith('/shop') || path.endsWith('/catalog')) {
                    link.classList.add('is-active');
                }
            });

            return; // в каталоге якоря не трогаем
        }

        // --------------------------------------------
        // РЕЖИМ 2: ГЛАВНАЯ — якоря + скролл
        // --------------------------------------------
        if (!document.body.classList.contains('home')) return;

        const hashLinks = menu.querySelectorAll('a[href*="#"]');
        if (!hashLinks.length) return;

        // Ищем ссылку "Главная"
        let homeLink = null;
        allLinks.forEach(function (link) {
            const href = link.getAttribute('href');
            if (!href) return;

            const url       = new URL(link.href, window.location.origin);
            const isHomeUrl = url.pathname === '/' || url.pathname === window.location.pathname;
            const hasHash   = url.hash && url.hash.length > 1;

            if (isHomeUrl && !hasHash) {
                homeLink = link;
            }
        });

        // Пары: ссылка → целевая секция
        const pairs = [];
        hashLinks.forEach(function (link) {
            const hash = link.getAttribute('href').split('#')[1];
            if (!hash) return;

            const section = document.getElementById(hash);
            if (section) pairs.push({ link: link, section: section });
        });

        if (!pairs.length) return;

        pairs.sort(function (a, b) {
            return a.section.offsetTop - b.section.offsetTop;
        });

        const TRIGGER_RATIO = 0.4;

        function updateActive() {
            const scrollPos = window.scrollY + window.innerHeight * TRIGGER_RATIO;
            let activeIndex = -1;

            pairs.forEach(function (pair, i) {
                if (pair.section.offsetTop <= scrollPos) {
                    activeIndex = i;
                }
            });

            pairs.forEach(function (pair, i) {
                pair.link.classList.toggle('is-active', i === activeIndex);
            });

            if (homeLink) {
                homeLink.classList.toggle('is-active', activeIndex === -1);
            }
        }

        // Плавный скролл при клике по якорю
        const headerOffset = 100;
        hashLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                const url = new URL(link.href, window.location.origin);

                if (url.pathname !== window.location.pathname) return;

                const target = document.querySelector(url.hash);
                if (!target) return;

                e.preventDefault();
                const y = target.getBoundingClientRect().top + window.scrollY - headerOffset;
                window.scrollTo({ top: y, behavior: 'smooth' });
                history.pushState(null, '', url.hash);
            });
        });

        window.addEventListener('scroll', updateActive, { passive: true });
        window.addEventListener('resize', updateActive);
        updateActive();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();