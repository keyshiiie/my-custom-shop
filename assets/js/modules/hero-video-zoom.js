// ============================================
// Scroll zoom для видео-превью в hero
// ============================================
(function () {
    'use strict';

    function init() {
        const video = document.querySelector('.hero-video');
        if (!video) return;

        const MIN_SCALE    = 0.5;
        const MAX_SCALE    = 1.0;
        const MIN_OPACITY  = 0.6;
        const MAX_OPACITY  = 1.0;

        function updateScale() {
            const rect         = video.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            const startTrigger = windowHeight;
            const endTrigger   = windowHeight * 0.4;

            let progress = (startTrigger - rect.top) / (startTrigger - endTrigger);
            progress = Math.max(0, Math.min(1, progress));

            const scale   = MIN_SCALE + (MAX_SCALE - MIN_SCALE) * progress;
            const opacity = MIN_OPACITY + (MAX_OPACITY - MIN_OPACITY) * progress;

            video.style.transform = 'scale(' + scale + ')';
            video.style.opacity = opacity;
        }

        window.addEventListener('scroll', updateScale, { passive: true });
        window.addEventListener('resize', updateScale);
        updateScale();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();