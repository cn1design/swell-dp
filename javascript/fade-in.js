(function () {
  'use strict';

  var STAGGER = 0.12; // 子要素の時差（秒）

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-fade-in');
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.1 });

  function observe(el, delay) {
    if (delay) el.style.transitionDelay = delay + 's';
    // ダブルRAFで opacity:0 を確実に描画してから監視開始
    // （即座に observe すると is-fade-in が付く前に描画されず transition が見えない）
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        observer.observe(el);
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {

    // body にクラスを付与（エディタ上では JS が動かないため opacity:0 が適用されない）
    document.body.classList.add('js-fade-ready');

    // 個別要素: .js-fade が付いた要素をそのまま監視
    document.querySelectorAll('.js-fade').forEach(function (el) {
      observe(el, 0);
    });

    // セクション単位: .js-fade-section の直下子要素を自動フェード（時差あり）
    document.querySelectorAll('.js-fade-section').forEach(function (section) {
      Array.from(section.children).forEach(function (child, i) {
        child.classList.add('js-fade');
        observe(child, i * STAGGER);
      });
    });

    // =====================================================
    // dp-enter-* スクロールアニメーション（ローディング無し時）
    // dp-before-load が付いていないページ（ローディング無効 or 対象外ページ）で
    // dp-enter-up / dp-enter-left / dp-enter-right をスクロール監視で表示する
    // =====================================================
    var html = document.documentElement;
    if (!html.classList.contains('dp-before-load') && !html.classList.contains('dp-loaded')) {
      html.classList.add('dp-scroll-ready');

      var enterObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-visible');
          enterObserver.unobserve(entry.target);
        });
      }, { threshold: 0.1 });

      document.querySelectorAll('.dp-enter-up, .dp-enter-left, .dp-enter-right').forEach(function (el) {
        // ダブルRAFで初期 opacity:0 を確実に描画してから監視開始
        requestAnimationFrame(function () {
          requestAnimationFrame(function () {
            enterObserver.observe(el);
          });
        });
      });
    }

  });
})();
