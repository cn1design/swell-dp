(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const footer = document.querySelector('#footer') || document.querySelector('.l-footer');

        if (!footer) return;

        // SWELL はスクロール時にヘッダーを JS クローンするため
        // querySelector（単一）ではクローン前の非表示要素を掴んでしまう。
        // querySelectorAll で全インスタンス（元＋クローン）に一括付与する。
        const observer = new IntersectionObserver(function (entries) {
            const hidden = entries[0].isIntersecting;
            document.querySelectorAll('.dp-vertical-right-ja').forEach(function (el) {
                el.classList.toggle('is-footer-hidden', hidden);
            });
        }, {
            threshold: 0,
            rootMargin: '0px 0px 80px 0px',
        });

        observer.observe(footer);
    });
})();
