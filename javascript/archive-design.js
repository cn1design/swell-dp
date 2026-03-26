'use strict';

/**
 * archive-design.js
 * デザインパターン一覧アーカイブ専用スクリプト
 * - フィルターグループ アコーディオン（bp幅以上: 3行, 未満: 1行）
 * - フィルタリング + URLパラメータ初期適用
 * - GIF トグル
 * - コピーボタン
 */

document.addEventListener('DOMContentLoaded', () => {
    const wrap = document.getElementById('dp-archive-wrap');
    if (!wrap) return;

    /* =========================================================
     * 0. アコーディオン
     * bp = 959px → 960px以上でPC判定
     * PC: 最大3行, SP: 最大1行
     * ========================================================= */
    const BP = 960;

    /**
     * btnsEl 内のボタンを maxRows 行まで表示するための高さを返す。
     * maxRows 以内に収まる場合は null（アコーディオン不要）。
     */
    function getCollapsedHeight(btnsEl, maxRows) {
        const btns = Array.from(btnsEl.querySelectorAll('.pl-filter-btn'));
        if (!btns.length) return null;

        const containerTop = btnsEl.getBoundingClientRect().top;
        let rowCount = 1;
        let prevTop   = btns[0].getBoundingClientRect().top;

        for (let i = 1; i < btns.length; i++) {
            const btnTop = btns[i].getBoundingClientRect().top;
            if (btnTop > prevTop + 2) {
                rowCount++;
                prevTop = btnTop;
                if (rowCount > maxRows) {
                    // (maxRows + 1)行目の開始位置 = 折りたたみ高さ
                    return btnTop - containerTop;
                }
            }
        }

        return null; // maxRows 以内に収まる
    }

    function destroyAccordion(group) {
        const btnsEl  = group.querySelector('.pl-filter-btns');
        const toggle  = group.querySelector('.pl-filter-toggle');
        if (btnsEl) {
            btnsEl.style.overflow   = '';
            btnsEl.style.maxHeight  = '';
            btnsEl.style.transition = '';
        }
        if (toggle) toggle.remove();
        group.classList.remove('is-collapsible');
    }

    function initAccordion(group) {
        const btnsEl = group.querySelector('.pl-filter-btns');
        if (!btnsEl) return;

        const maxRows    = window.innerWidth >= BP ? 3 : 1;
        const collapsedH = getCollapsedHeight(btnsEl, maxRows);
        if (collapsedH === null) return;

        btnsEl.style.overflow   = 'hidden';
        btnsEl.style.maxHeight  = collapsedH + 'px';
        btnsEl.style.transition = 'max-height 0.3s ease';
        group.classList.add('is-collapsible');

        const toggle = document.createElement('button');
        toggle.className = 'pl-filter-toggle';
        toggle.type = 'button';
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', '展開');
        toggle.textContent = '▼';
        group.appendChild(toggle);

        toggle.addEventListener('click', () => {
            const isOpen = toggle.classList.contains('is-open');
            btnsEl.style.maxHeight = isOpen ? collapsedH + 'px' : btnsEl.scrollHeight + 'px';
            toggle.classList.toggle('is-open', !isOpen);
            toggle.setAttribute('aria-expanded', String(!isOpen));
        });
    }

    const filterGroups = Array.from(wrap.querySelectorAll('.pl-filter-group'));
    filterGroups.forEach(initAccordion);

    // bp をまたぐリサイズ時に再初期化
    let resizeTimer;
    let prevIsWide = window.innerWidth >= BP;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const nowIsWide = window.innerWidth >= BP;
            if (nowIsWide !== prevIsWide) {
                prevIsWide = nowIsWide;
                filterGroups.forEach(group => {
                    destroyAccordion(group);
                    initAccordion(group);
                });
            }
        }, 200);
    });

    /* =========================================================
     * 1. フィルタリング
     * ========================================================= */
    let activeSection  = 'all';
    let activeIndustry = 'all';

    const applyFilter = () => {
        const cards = wrap.querySelectorAll('.pl-card');
        let visible = 0;

        cards.forEach((card) => {
            const secMatch = activeSection  === 'all' || ` ${card.dataset.section}  `.includes(` ${activeSection} `);
            const indMatch = activeIndustry === 'all' || ` ${card.dataset.industry} `.includes(` ${activeIndustry} `);

            if (secMatch && indMatch) {
                card.classList.remove('is-hidden');
                visible++;
            } else {
                card.classList.add('is-hidden');
            }
        });

        const countNum = wrap.querySelector('.pl-count-num');
        if (countNum) countNum.textContent = visible;

        const noResult = document.getElementById('pl-no-result');
        if (noResult) noResult.style.display = visible === 0 ? 'block' : 'none';
    };

    const activateButton = (group, value) => {
        group.querySelectorAll('.pl-filter-btn').forEach((b) => {
            b.classList.toggle('is-active', b.dataset.value === value);
        });
    };

    // URLパラメータで初期状態を適用
    const params      = new URLSearchParams(window.location.search);
    const initSection  = params.get('section')  || 'all';
    const initIndustry = params.get('industry') || 'all';

    wrap.querySelectorAll('.pl-filter-btns').forEach((group) => {
        const type   = group.dataset.filterType;
        const target = type === 'section' ? initSection : initIndustry;

        if (target !== 'all') {
            const found = group.querySelector(`[data-value="${target}"]`);
            if (found) {
                activateButton(group, target);
                if (type === 'section')  activeSection  = target;
                if (type === 'industry') activeIndustry = target;
            }
        }
    });

    applyFilter();

    // フィルターボタンクリック（Event Delegation）
    wrap.addEventListener('click', (e) => {
        const btn = e.target.closest('.pl-filter-btn');
        if (!btn) return;

        const group = btn.closest('.pl-filter-btns');
        if (!group) return;

        const type  = group.dataset.filterType;
        const value = btn.dataset.value;

        activateButton(group, value);

        if (type === 'section')  activeSection  = value;
        if (type === 'industry') activeIndustry = value;

        applyFilter();
    });

    /* =========================================================
     * 2. GIF トグル（Event Delegation）
     * ========================================================= */
    const isTouch = window.matchMedia('(hover: none)').matches;

    if (isTouch) {
        wrap.addEventListener('click', (e) => {
            const thumb = e.target.closest('.pl-card-thumb.has-gif');
            if (!thumb || e.target.closest('.pl-btn')) return;

            const gifImg = thumb.querySelector('.pl-thumb-gif');
            if (gifImg && thumb.dataset.gif && !gifImg.src) {
                gifImg.src = thumb.dataset.gif;
            }
            thumb.classList.toggle('is-gif-active');
        });
    } else {
        wrap.addEventListener('mouseover', (e) => {
            const thumb = e.target.closest('.pl-card-thumb.has-gif');
            if (!thumb) return;

            const gifImg = thumb.querySelector('.pl-thumb-gif');
            if (gifImg && thumb.dataset.gif && !gifImg.src) {
                gifImg.src = thumb.dataset.gif;
            }
        });
    }

    /* =========================================================
     * 3. コピーボタン（Event Delegation）
     * ========================================================= */
    wrap.addEventListener('click', (e) => {
        const btn = e.target.closest('.pl-btn--copy:not(.is-disabled)');
        if (!btn) return;

        const code   = btn.dataset.code;
        const copied = btn.dataset.labelCopied || 'コピー完了 ✓';
        const def    = btn.dataset.labelDefault || 'コピーする';

        navigator.clipboard.writeText(code).then(() => {
            btn.classList.add('is-copied');
            btn.innerHTML =
                '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>' +
                copied;
            setTimeout(() => {
                btn.classList.remove('is-copied');
                btn.innerHTML =
                    '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H8V7h11v14z"/></svg>' +
                    def;
            }, 2500);
        });
    });
});
