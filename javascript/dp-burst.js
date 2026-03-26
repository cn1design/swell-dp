'use strict';

/**
 * dp-burst.js
 * コピーボタン共通 — サンバーストアニメーション
 * 対象: .pl-btn--copy / .dp-bace-css-btn / .dp-copy-btn
 */

(function () {

    /**
     * ボタン中心座標からパーティクルを放射する
     * @param {HTMLElement} btn
     */
    function triggerBurst(btn) {
        const rect   = btn.getBoundingClientRect();
        const cx     = rect.left + rect.width  / 2;
        const cy     = rect.top  + rect.height / 2;

        const COUNT  = 8;
        // メイン色・白・薄いメイン色を交互に
        const COLORS = [
            'var(--color_main)',
            '#ffffff',
            'var(--color_main)',
            'var(--color_main_thin)',
            '#ffffff',
            'var(--color_main)',
            'var(--color_main_thin)',
            '#ffffff',
        ];

        for (let i = 0; i < COUNT; i++) {
            // 均等角度 + 少しランダムにずらして自然な広がりに
            const baseAngle = (i / COUNT) * Math.PI * 2 - Math.PI / 2;
            const angle     = baseAngle + (Math.random() - 0.5) * 0.4;
            const dist      = 26 + Math.random() * 18;
            const size      = Math.round(5 + Math.random() * 5);
            const delay     = Math.round(Math.random() * 50);

            const p = document.createElement('span');
            p.className = 'dp-burst-particle';
            p.style.cssText =
                'left:'   + cx + 'px;' +
                'top:'    + cy + 'px;' +
                'width:'  + size + 'px;' +
                'height:' + size + 'px;' +
                'background:' + COLORS[i] + ';' +
                'animation-delay:' + delay + 'ms;' +
                '--dx:' + (Math.cos(angle) * dist).toFixed(1) + 'px;' +
                '--dy:' + (Math.sin(angle) * dist).toFixed(1) + 'px;';

            document.body.appendChild(p);
            p.addEventListener('animationend', function () { p.remove(); }, { once: true });
        }
    }

    // Document レベル Event Delegation — 全ページ共通で動作
    document.addEventListener('click', function (e) {
        var btn = e.target.closest(
            '.pl-btn--copy:not(.is-disabled), .dp-bace-css-btn, .dp-copy-btn'
        );
        if (btn) triggerBurst(btn);
    });

}());
