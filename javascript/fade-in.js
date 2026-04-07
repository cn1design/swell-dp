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
    observer.observe(el);
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

  });
})();
