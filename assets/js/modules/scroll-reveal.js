// ============================================
// Scroll reveal — появление блоков при скролле
// ============================================
(function () {
    'use strict';

    function init() {
        const revealElements = document.querySelectorAll('.reveal');
        if (!revealElements.length) return;

        // Если IntersectionObserver не поддерживается — показываем всё сразу
        if (!('IntersectionObserver' in window)) {
            revealElements.forEach(function (el) {
                el.classList.add('is-visible');
            });
            return;
        }

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '0px 0px -10% 0px',
            threshold: 0.05
        });

        revealElements.forEach(function (el) {
            observer.observe(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();