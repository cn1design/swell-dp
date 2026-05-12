/**
 * SWELL-DP スライダー テンプレート
 * ==========================================================
 * 新しいスライダーパターンを実装する際はこのファイルをコピーして使う。
 *
 * 使い方:
 *   1. このファイルを javascript/dp-{name}-slider.js としてコピー
 *   2. {SLIDER_TRIGGER}  → ブロックコードに付与する JS トリガークラス名
 *   3. {OUTER_CLASS}     → JS が生成する outer wrapper のクラス名
 *   4. LOOP_MODE         → true = ループあり / false = 端で止まる
 *   5. MIN_SLIDES        → スライダー起動に必要な最低スライド数
 *   6. inline-css の <script> に貼り付けるか、javascript/ にファイルとして追加してエンキュー
 *
 * 重要: このスクリプトは output/inline-css/{slug}.html 内の <script> として
 *       インライン化して使用すること（二重初期化防止ガード付き）。
 * ==========================================================
 */

(function () {
  'use strict';

  /* --------------------------------------------------------
   * 設定値（パターンごとに変更する）
   * -------------------------------------------------------- */
  var SLIDER_TRIGGER = '.js-works-slider';   // ブロックコードに付与するクラス
  var OUTER_CLASS    = 'dp-works-slider-c-outer'; // outer wrapper クラス
  var LOOP_MODE      = true;                 // ループするか否か
  var MIN_SLIDES     = 2;                    // 最低スライド枚数（dp-works-slider-b は 4）

  /* --------------------------------------------------------
   * SWELL/Gutenberg DOM 構造メモ（重要）
   * --------------------------------------------------------
   * wp:group layout:default は以下のように描画される:
   *
   *   <div class="wp-block-group {SLIDER_TRIGGER}">           ← slider 変数
   *     <div class="wp-block-group__inner-blocks">            ← inner 変数（ここに dp-works-slider-inner が付く）
   *       <div class="wp-block-group js-works-slider-item">  ← slide 要素
   *       ...
   *     </div>
   *   </div>
   *
   * → allSlides[0].parentElement = "wp-block-group__inner-blocks"
   * → CSS は「.container .dp-works-slider-inner」（子孫セレクター）を使う
   *   ❌ .container.dp-works-slider-inner（複合クラス）は絶対に使わない
   * -------------------------------------------------------- */

  function initSlider(slider) {
    /* 二重初期化防止 */
    if (slider.querySelector('.dp-slider-nav')) return;

    var allSlides = Array.from(slider.querySelectorAll('.js-works-slider-item'));
    if (allSlides.length < MIN_SLIDES) return;

    /* inner = アイテムの直接親（SWELL では inner-blocks wrapper） */
    var inner  = allSlides[0].parentElement;
    var slides = Array.from(inner.querySelectorAll(':scope > .js-works-slider-item'));
    var count  = slides.length;

    /* ---- outer wrapper 生成 ---- */
    var outer = document.createElement('div');
    outer.className = OUTER_CLASS;
    slider.parentNode.insertBefore(outer, slider);
    outer.appendChild(slider);

    /* ---- ナビゲーションボタン生成 ---- */
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
    outer.appendChild(nav);

    /* ---- ループモード: クローンを先頭・末尾に追加 ---- */
    if (LOOP_MODE) {
      slides.forEach(function (s) {
        var c = s.cloneNode(true);
        c.setAttribute('aria-hidden', 'true');
        inner.appendChild(c);
      });
      slides.slice().reverse().forEach(function (s) {
        var c = s.cloneNode(true);
        c.setAttribute('aria-hidden', 'true');
        inner.insertBefore(c, inner.firstChild);
      });
    }

    /* ---- CSS クラス付与（スライダー起動） ---- */
    inner.classList.add('dp-works-slider-inner');
    slider.classList.add('is-slider-active');

    /* ---- ユーティリティ関数 ---- */
    function getScrollAmount() {
      /* 毎回 DOM から取得（クローン追加後も正確な値を得るため） */
      var items = inner.querySelectorAll(':scope > .js-works-slider-item');
      if (items.length < 2) return inner.clientWidth;
      var r0 = items[0].getBoundingClientRect();
      var r1 = items[1].getBoundingClientRect();
      return Math.abs(r1.left - r0.left);
    }

    function updateActiveItem() {
      /* ビューポート中央に最も近いアイテムに is-active を付与 */
      var items = Array.from(inner.querySelectorAll(':scope > .js-works-slider-item'));
      var center = inner.getBoundingClientRect().left + inner.clientWidth / 2;
      var closest = null;
      var minDist = Infinity;
      items.forEach(function (s) {
        var r = s.getBoundingClientRect();
        var d = Math.abs(r.left + r.width / 2 - center);
        if (d < minDist) { minDist = d; closest = s; }
      });
      items.forEach(function (s) { s.classList.toggle('is-active', s === closest); });
    }

    /* ---- 非ループ: 端ボタンの disabled 表示 ---- */
    function updateButtons() {
      prevBtn.classList.toggle('is-edge', inner.scrollLeft <= 1);
      nextBtn.classList.toggle('is-edge',
        inner.scrollLeft + inner.clientWidth >= inner.scrollWidth - 1);
    }

    /* ---- ループ: スクロール停止後に位置補正 ---- */
    var loopTimer = null;
    function checkLoop() {
      var amount    = getScrollAmount();
      var loopWidth = amount * count;
      if (inner.scrollLeft < loopWidth) {
        inner.scrollLeft += loopWidth;          /* 先頭クローン域 → 末尾実アイテムへ */
      } else if (inner.scrollLeft >= loopWidth * 2) {
        inner.scrollLeft -= loopWidth;          /* 末尾クローン域 → 先頭実アイテムへ */
      }
    }

    /* ---- イベント ---- */
    prevBtn.addEventListener('click', function () {
      inner.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
    });
    nextBtn.addEventListener('click', function () {
      inner.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
    });

    inner.addEventListener('scroll', function () {
      if (LOOP_MODE) {
        clearTimeout(loopTimer);
        loopTimer = setTimeout(checkLoop, 80); /* スクロール停止後に補正 */
      } else {
        updateButtons();
      }
      updateActiveItem();
    }, { passive: true });

    /* ---- 初期化 ---- */
    requestAnimationFrame(function () {
      if (LOOP_MODE) {
        /* 先頭クローンをスキップして実アイテム先頭へ */
        inner.scrollLeft = getScrollAmount() * count;
      } else {
        updateButtons();
      }
      updateActiveItem();
    });
  }

  /* ---- DOMContentLoaded でバインド ---- */
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll(SLIDER_TRIGGER).forEach(initSlider);
  });

})();
