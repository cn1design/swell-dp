(function () {
  'use strict';

  function initWorksSlider(slider) {
    // `:scope >` を使わずに全子孫から取得し、実際の親要素を特定
    const allSlides = Array.from(slider.querySelectorAll('.js-works-slider-item'));
    if (allSlides.length < 2) return;

    // スライドの実際の親要素（SWELL/Gutenberg の inner container 対策）
    const inner = allSlides[0].parentElement;
    const slides = Array.from(inner.querySelectorAll(':scope > .js-works-slider-item'));

    // アウターラッパーを生成（矢印ボタンの絶対配置基点）
    const outer = document.createElement('div');
    outer.className = 'dp-works-slider-outer';
    slider.parentNode.insertBefore(outer, slider);
    outer.appendChild(slider);

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
    outer.appendChild(nav);

    // スクロール量 = 隣スライドの左端差分（スライド幅 + gap）
    function getScrollAmount() {
      const r0 = slides[0].getBoundingClientRect();
      const r1 = slides[1].getBoundingClientRect();
      return Math.abs(r1.left - r0.left);
    }

    function updateButtons() {
      const atStart = inner.scrollLeft <= 1;
      const atEnd = inner.scrollLeft + inner.clientWidth >= inner.scrollWidth - 1;
      prevBtn.classList.toggle('is-edge', atStart);
      nextBtn.classList.toggle('is-edge', atEnd);
    }

    prevBtn.addEventListener('click', function () {
      inner.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
    });

    nextBtn.addEventListener('click', function () {
      inner.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
    });

    inner.addEventListener('scroll', updateButtons, { passive: true });

    // inner にスクロールコンテナ用クラスを付与 → CSS で flex/overflow 適用
    inner.classList.add('dp-works-slider-inner');
    slider.classList.add('is-slider-active');
    updateButtons();
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-works-slider').forEach(initWorksSlider);
  });
})();
