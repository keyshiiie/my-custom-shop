// ============================================
// Переключение главной картинки по клику на миниатюру
// ============================================
(function () {
    'use strict';

    function init() {
        const mainImg  = document.getElementById('product-gallery-main');
        const thumbs   = document.querySelectorAll('.product-gallery__thumb');

        if (!mainImg || !thumbs.length) return;

        thumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                // Меняем src главного изображения
                const newSrc = thumb.dataset.imageSrc;
                if (newSrc) {
                    mainImg.src = newSrc;
                    mainImg.srcset = '';
                }

                // Переключаем активный класс
                thumbs.forEach(function (t) {
                    t.classList.remove('is-active');
                });
                thumb.classList.add('is-active');
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();