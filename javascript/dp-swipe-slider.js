(function () {
  'use strict';

  // SCSS の $breakpoints.bp と合わせる
  var BP_PC = 959;

  function initSlider(slider) {
    var inner = slider.querySelector('.swell-block-columns__inner');
    var columns = slider.querySelectorAll('.swell-block-column');
    if (!inner || columns.length < 2) return;

    // ナビゲーションボタン生成
    // position: absolute でカードエリアに重ねるため height: 100% のオーバーレイとして挿入
    var nav = document.createElement('div');
    nav.className = 'dp-slider-nav';

    var prevBtn = document.createElement('button');
    prevBtn.type = 'button';
    prevBtn.className = 'dp-slider-nav__btn dp-slider-nav__prev';
    prevBtn.setAttribute('aria-label', '前へ');
    var prevIcon = document.createElement('span');
    prevIcon.className = 'icon-chevron-left';
    prevIcon.setAttribute('aria-hidden', 'true');
    prevBtn.appendChild(prevIcon);

    var nextBtn = document.createElement('button');
    nextBtn.type = 'button';
    nextBtn.className = 'dp-slider-nav__btn dp-slider-nav__next';
    nextBtn.setAttribute('aria-label', '次へ');
    var nextIcon = document.createElement('span');
    nextIcon.className = 'icon-chevron-right';
    nextIcon.setAttribute('aria-hidden', 'true');
    nextBtn.appendChild(nextIcon);

    nav.appendChild(prevBtn);
    nav.appendChild(nextBtn);
    slider.appendChild(nav);

    // PC（> BP_PC）かつカード4枚以上の場合のみスライダー発動
    // タブレット・SP は枚数問わず常に発動
    function shouldActivate() {
      if (window.innerWidth <= BP_PC) return true;
      return columns.length >= 4;
    }

    // 1スクロール量 = 隣カラムの左端差分（実測値）
    function getScrollAmount() {
      var cols = inner.querySelectorAll('.swell-block-column');
      if (cols.length < 2) return inner.clientWidth;
      var r0 = cols[0].getBoundingClientRect();
      var r1 = cols[1].getBoundingClientRect();
      return Math.abs(r1.left - r0.left);
    }

    // disabled 廃止 → .is-edge クラスで opacity 制御
    function updateButtons() {
      var atStart = inner.scrollLeft <= 1;
      var atEnd   = inner.scrollLeft + inner.clientWidth >= inner.scrollWidth - 1;
      prevBtn.classList.toggle('is-edge', atStart);
      nextBtn.classList.toggle('is-edge', atEnd);
    }

    function activate() {
      slider.classList.add('is-slider-active');
      updateButtons();
    }

    function deactivate() {
      slider.classList.remove('is-slider-active');
      inner.scrollLeft = 0;
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

    inner.addEventListener('scroll', updateButtons, { passive: true });
    window.addEventListener('resize', refresh, { passive: true });

    refresh();
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-swipe-slider').forEach(initSlider);
  });
})();
