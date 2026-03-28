/**
 * dp-like.js
 * デザインパターン いいねボタン
 * - LocalStorage で状態管理（認証不要）
 * - Optimistic Updates（即時反映 → API確定）
 * - CustomEvent で同一セッション内の全インスタンス同期
 */
(() => {
    'use strict';

    const STORAGE_KEY = 'dp_liked';
    const API_URL = (window.dpLikeSettings && window.dpLikeSettings.apiUrl)
        ? window.dpLikeSettings.apiUrl
        : '/wp-json/dp/v1/like';

    // SVG状態はCSSクラス（.is-liked）で制御するため、innerHTML操作は不要

    /* ---- LocalStorage helpers ---- */
    function getLikedSet() {
        try {
            return new Set(JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]'));
        } catch {
            return new Set();
        }
    }
    function saveLikedSet(set) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify([...set]));
    }
    function isLiked(postId) {
        return getLikedSet().has(postId);
    }

    /* ---- DOM helpers ---- */
    function getCountEl(btn) {
        return btn.querySelector('.pl-like-count')
            || btn.querySelector('.dp-like-fixed__count')
            || null;
    }

    function setButtonState(btn, liked, count) {
        const countEl = getCountEl(btn);

        btn.classList.toggle('is-liked', liked);
        btn.setAttribute('aria-pressed', String(liked));

        if (countEl) {
            if (count > 0) {
                countEl.textContent = count;
                countEl.hidden = false;
            } else {
                countEl.textContent = '';
                countEl.hidden = true;
            }
        }
    }

    function triggerPop(btn) {
        btn.classList.remove('is-pop');
        void btn.offsetWidth; // reflow
        btn.classList.add('is-pop');
        setTimeout(() => btn.classList.remove('is-pop'), 400);
    }

    /* ---- API call ---- */
    async function postLike(postId, action) {
        const res = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId, action }),
        });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    }

    /* ---- Sync event ---- */
    function broadcast(postId, count, liked) {
        document.dispatchEvent(new CustomEvent('dp:like-updated', {
            detail: { postId, count, liked }
        }));
    }

    /* ---- Initialize single button ---- */
    function initButton(btn) {
        const postId = parseInt(btn.dataset.postId, 10);
        if (!postId) return;

        let count = parseInt(btn.dataset.likeCount || '0', 10);

        // 初期状態
        setButtonState(btn, isLiked(postId), count);

        // 他インスタンスからの同期を受信
        document.addEventListener('dp:like-updated', (e) => {
            if (e.detail.postId === postId) {
                count = e.detail.count;
                setButtonState(btn, e.detail.liked, count);
            }
        });

        // クリック
        btn.addEventListener('click', async () => {
            if (btn.classList.contains('is-loading')) return;

            const wasLiked       = isLiked(postId);
            const action         = wasLiked ? 'unlike' : 'like';
            const newLiked       = !wasLiked;
            const optimisticCnt  = wasLiked ? Math.max(0, count - 1) : count + 1;

            // Optimistic update
            btn.classList.add('is-loading');
            const storage = getLikedSet();
            newLiked ? storage.add(postId) : storage.delete(postId);
            saveLikedSet(storage);

            count = optimisticCnt;
            setButtonState(btn, newLiked, optimisticCnt);
            triggerPop(btn);
            broadcast(postId, optimisticCnt, newLiked);

            try {
                const data = await postLike(postId, action);
                const serverCnt = typeof data.count === 'number' ? data.count : optimisticCnt;
                count = serverCnt;
                setButtonState(btn, newLiked, serverCnt);
                broadcast(postId, serverCnt, newLiked);
            } catch {
                // 失敗時は巻き戻し
                const revertStorage = getLikedSet();
                wasLiked ? revertStorage.add(postId) : revertStorage.delete(postId);
                saveLikedSet(revertStorage);
                count = wasLiked ? optimisticCnt + 1 : Math.max(0, optimisticCnt - 1);
                setButtonState(btn, wasLiked, count);
                broadcast(postId, count, wasLiked);
            } finally {
                btn.classList.remove('is-loading');
            }
        });
    }

    /* ---- Entry point ---- */
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.pl-like-btn, .dp-like-fixed__btn, .dp-like-inline')
            .forEach(initButton);
    });
})();
