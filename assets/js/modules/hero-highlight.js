// ============================================
// Анимация смены слова в hero highlight
// ============================================
(function () {
    'use strict';

    function init() {
        const highlightEl = document.querySelector('.hero-title .highlight');
        const wordEl      = document.querySelector('.hero-title .highlight__word');
        if (!highlightEl || !wordEl) return;

        const words = ['время', 'деньги', 'силы', 'ресурсы', 'нервы'];
        let currentIndex = 0;

        const INTERVAL      = 2800;   // 2.8s между сменами
        const ANIM_DURATION = 500;    // совпадает с transition
        const ENTER_DELAY   = 20;     // сброс transition

        function changeWord() {
            highlightEl.classList.add('is-leaving');

            setTimeout(function () {
                currentIndex = (currentIndex + 1) % words.length;
                wordEl.textContent = words[currentIndex];

                highlightEl.classList.remove('is-leaving');
                highlightEl.classList.add('is-entering');

                setTimeout(function () {
                    highlightEl.classList.remove('is-entering');
                }, ENTER_DELAY);
            }, ANIM_DURATION);
        }

        setInterval(changeWord, INTERVAL);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();