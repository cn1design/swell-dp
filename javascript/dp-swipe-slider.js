(function () {
  'use strict';

  // SCSS の $breakpoints.bp と合わせる
  const BP_PC = 959;

  function initSlider(slider) {
    const inner = slider.querySelector('.swell-block-columns__inner');
    const columns = slider.querySelectorAll('.swell-block-column');
    if (!inner || columns.length < 2) return;
    if (slider.querySelector('.dp-slider-nav')) return; // 二重初期化防止

    // ナビゲーションボタン生成
    const nav = document.createElement('div');
    nav.className = 'dp-slider-nav';

    const prevBtn = document.createElement('button');
    prevBtn.type = 'button';
    prevBtn.className = 'dp-slider-nav__btn dp-slider-nav__prev';
    prevBtn.setAttribute('aria-label', '前へ');
    const prevIcon = document.createElement('span');
    prevIcon.className = 'icon-chevron-left';
    prevIcon.setAttribute('aria-hidden', 'true');
    prevBtn.appendChild(prevIcon);

    const nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.className = 'dp-slider-nav__btn dp-slider-nav__next';
    nextBtn.setAttribute('aria-label', '次へ');
    const nextIcon = document.createElement('span');
    nextIcon.className = 'icon-chevron-right';
    nextIcon.setAttribute('aria-hidden', 'true');
    nextBtn.appendChild(nextIcon);

    nav.appendChild(prevBtn);
    nav.appendChild(nextBtn);
    slider.appendChild(nav);

    // ページネーションバーコンテナ（ドットはactivate時に動的生成）
    const dotsWrap = document.createElement('div');
    dotsWrap.className = 'dp-slider-dots';
    dotsWrap.setAttribute('role', 'tablist');
    dotsWrap.setAttribute('aria-label', 'スライダーナビゲーション');
    slider.appendChild(dotsWrap);

    let dotItems = [];
    let currentPageCount = 0;

    // PC（> BP_PC）かつカード4枚以上の場合のみスライダー発動
    function shouldActivate() {
      if (window.innerWidth <= BP_PC) return true;
      return columns.length >= 4;
    }

    // 1スクロール量 = 隣カラムの左端差分（実測値）
    function getScrollAmount() {
      const cols = inner.querySelectorAll('.swell-block-column');
      if (cols.length < 2) return inner.clientWidth;
      const r0 = cols[0].getBoundingClientRect();
      const r1 = cols[1].getBoundingClientRect();
      return Math.abs(r1.left - r0.left);
    }

    // 必要なページ数 = 実際にスクロールできる回数 + 1
    // 「maxScrollLeft / scrollAmount」 で何ステップ動けるかを算出
    function getPageCount() {
      const amount = getScrollAmount();
      if (amount === 0) return 1;
      const maxScroll = inner.scrollWidth - inner.clientWidth;
      if (maxScroll <= 0) return 1;
      return Math.round(maxScroll / amount) + 1;
    }

    // 現在表示中のインデックス
    function getCurrentIndex() {
      const amount = getScrollAmount();
      if (amount === 0) return 0;
      return Math.round(inner.scrollLeft / amount);
    }

    // 指定インデックスのカードへスクロール
    function scrollToIndex(index) {
      const amount = getScrollAmount();
      inner.scrollTo({ left: amount * index, behavior: 'smooth' });
    }

    // ページ数が変わった場合のみドットを再生成
    function rebuildDots() {
      const pageCount = getPageCount();
      if (pageCount === currentPageCount) return;

      currentPageCount = pageCount;
      dotsWrap.innerHTML = '';
      dotItems = [];

      for (var i = 0; i < pageCount; i++) {
        (function (index) {
          const dot = document.createElement('button');
          dot.type = 'button';
          dot.className = 'dp-slider-dot' + (index === 0 ? ' is-active' : '');
          dot.setAttribute('role', 'tab');
          dot.setAttribute('aria-label', (index + 1) + 'ページ目');
          dot.setAttribute('aria-selected', index === 0 ? 'true' : 'false');
          dot.addEventListener('click', function () {
            scrollToIndex(index);
          });
          dotsWrap.appendChild(dot);
          dotItems.push(dot);
        })(i);
      }
    }

    function updateButtons() {
      const atStart = inner.scrollLeft <= 1;
      const atEnd   = inner.scrollLeft + inner.clientWidth >= inner.scrollWidth - 1;
      prevBtn.classList.toggle('is-edge', atStart);
      nextBtn.classList.toggle('is-edge', atEnd);
    }

    function updateDots() {
      const current = getCurrentIndex();
      dotItems.forEach(function (dot, i) {
        const active = i === current;
        dot.classList.toggle('is-active', active);
        dot.setAttribute('aria-selected', active ? 'true' : 'false');
      });
    }

    function activate() {
      slider.classList.add('is-slider-active');
      // レイアウト確定後に計測（requestAnimationFrame で1フレーム待つ）
      requestAnimationFrame(function () {
        rebuildDots();
        updateButtons();
        updateDots();
      });
    }

    function deactivate() {
      slider.classList.remove('is-slider-active');
      inner.scrollLeft = 0;
      currentPageCount = 0;
      dotsWrap.innerHTML = '';
      dotItems = [];
    }

    function refresh() {
      shouldActivate() ? activate() : deactivate();
    }

    prevBtn.addEventListener('click', function () {
      inner.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
    });

    nextBtn.addEventListener('click', function () {
      inner.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
    });

    inner.addEventListener('scroll', function () {
      updateButtons();
      updateDots();
    }, { passive: true });

    window.addEventListener('resize', refresh, { passive: true });

    refresh();
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-swipe-slider').forEach(initSlider);
  });
})();
