'use strict';

/**
 * dp-base-css.js
 * DP ベースCSS コピーボタン共通ハンドラ
 * archive-design_pattern / page-design_pattern_standard 両ページで動作
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.dp-bace-css-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            const targetId = btn.dataset.target;
            const el = targetId ? document.getElementById(targetId) : null;
            if (!el) return;

            // textarea は .value、それ以外は .textContent で取得
            const css = el.tagName === 'TEXTAREA' ? el.value : el.textContent;

            navigator.clipboard.writeText(css).then(() => {
                const original = btn.innerHTML;
                btn.classList.add('is-copied');
                btn.innerHTML =
                    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>' +
                    'コピー完了 ✓';
                setTimeout(() => {
                    btn.classList.remove('is-copied');
                    btn.innerHTML = original;
                }, 2500);
            });
        });
    });
});
