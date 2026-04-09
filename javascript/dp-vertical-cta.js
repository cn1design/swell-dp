(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const footer = document.querySelector('#footer') || document.querySelector('.l-footer');

        if (!footer) return;

        // .dp-vertical-right-ja は wp_footer 経由でヘッダー外に独立配置。
        // querySelectorAll で全インスタンスを対象にしてフッター回避クラスを付与する。
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
