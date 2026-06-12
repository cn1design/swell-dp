(function () {
    'use strict';

    const MIN_DISPLAY = 600; // ローディング最低表示時間（ms）: 高速回線でのフラッシュ防止

    const startTime = Date.now();

    function reveal() {
        // オーバーレイをフェードアウト → transitionend後にDOM削除
        const overlay = document.getElementById('dp-loading-overlay');
        if (overlay) {
            overlay.classList.add('is-hidden');
            overlay.addEventListener('transitionend', function () {
                if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
            }, { once: true });
        }

        // ヒーロー入場アニメーション開始
        const html = document.documentElement;
        html.classList.remove('dp-before-load');
        html.classList.add('dp-loaded');
    }

    window.addEventListener('load', function () {
        const elapsed = Date.now() - startTime;
        const wait = Math.max(0, MIN_DISPLAY - elapsed);
        setTimeout(reveal, wait);
    });
})();
