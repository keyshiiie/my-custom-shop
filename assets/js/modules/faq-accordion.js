// ============================================
// FAQ: плавное открытие/закрытие
// ============================================
(function () {
    'use strict';

    function openFaq(item, answer) {
        item.open = true;

        // 1. Фиксируем нулевую высоту мгновенно, без анимации
        answer.style.transition = 'none';
        answer.style.maxHeight = '0px';

        // 2. Форсируем reflow
        void answer.offsetHeight;

        // 3. Возвращаем transition и ставим целевую высоту
        requestAnimationFrame(function () {
            answer.style.transition = '';
            answer.style.maxHeight = answer.scrollHeight + 'px';
        });

        // 4. После завершения — снимаем ограничение
        answer.addEventListener('transitionend', function handler(e) {
            if (e.propertyName !== 'max-height') return;
            answer.style.maxHeight = 'none';
            answer.removeEventListener('transitionend', handler);
        });
    }

    function closeFaq(item, answer) {
        // 1. Фиксируем текущую высоту
        answer.style.transition = 'none';
        answer.style.maxHeight = answer.scrollHeight + 'px';

        // 2. Форсируем reflow
        void answer.offsetHeight;

        // 3. Анимируем до нуля
        requestAnimationFrame(function () {
            answer.style.transition = '';
            answer.style.maxHeight = '0px';
        });

        // 4. Ждём завершения
        let closed = false;
        const finish = function () {
            if (closed) return;
            closed = true;
            item.open = false;
            answer.removeEventListener('transitionend', onEnd);
            clearTimeout(fallbackTimer);
        };
        const onEnd = function (e) {
            if (e.propertyName !== 'max-height') return;
            finish();
        };
        answer.addEventListener('transitionend', onEnd);

        // 5. Fallback на случай, если transitionend не сработал
        const fallbackTimer = setTimeout(finish, 500);
    }

    function init() {
        document.querySelectorAll('.faq-item').forEach(function (item) {
            const answer   = item.querySelector('.faq-item__answer');
            const question = item.querySelector('.faq-item__question');
            if (!answer || !question) return;

            // Если изначально открыт — снимаем ограничение высоты
            if (item.open) {
                answer.style.maxHeight = 'none';
            }

            question.addEventListener('click', function (e) {
                e.preventDefault();

                if (item.open) {
                    closeFaq(item, answer);
                } else {
                    // Закрываем все остальные открытые
                    document.querySelectorAll('.faq-item[open]').forEach(function (other) {
                        if (other !== item) {
                            const otherAnswer = other.querySelector('.faq-item__answer');
                            if (otherAnswer) closeFaq(other, otherAnswer);
                        }
                    });
                    openFaq(item, answer);
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();