// ============================================
// Подсветка активного пункта меню
// Работает только на главной странице
// ============================================
(function () {
    'use strict';

    function init() {
        // Только на главной
        if (!document.body.classList.contains('home')) return;

        const menu = document.querySelector('.nav-menu');
        if (!menu) return;

        const allLinks  = menu.querySelectorAll('a');
        const hashLinks = menu.querySelectorAll('a[href*="#"]');
        if (!hashLinks.length) return;

        // Ищем ссылку "Главная" — ведёт на корень без якоря
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

        // Сортируем по вертикали — на случай, если порядок в меню
        // не совпадает с порядком секций
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

            // Подсветка якорей
            pairs.forEach(function (pair, i) {
                pair.link.classList.toggle('is-active', i === activeIndex);
            });

            // Подсветка "Главная", если ни один якорь не активен
            if (homeLink) {
                homeLink.classList.toggle('is-active', activeIndex === -1);
            }
        }

        // Плавный скролл при клике по якорю
        const headerOffset = 100;
        hashLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                const url = new URL(link.href, window.location.origin);

                // Только если ссылка ведёт на текущую страницу
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