/**
 * fade-slide.js
 *
 * .dp-fade-slider 内の .wp-block-image を一定間隔でフェード切り替えする。
 * CSS の is-initialized / is-active クラスと連動。
 */
document.addEventListener('DOMContentLoaded', function () {
  var INTERVAL = 4000; // 切り替え間隔（ms）

  document.querySelectorAll('.dp-fade-slider').forEach(function (slider) {
    var images = slider.querySelectorAll('.wp-block-image');
    if (images.length < 2) return;

    // 初期化
    var currentIndex = 0;
    images[currentIndex].classList.add('is-active');
    slider.classList.add('is-initialized');

    // 自動切り替え
    setInterval(function () {
      images[currentIndex].classList.remove('is-active');
      currentIndex = (currentIndex + 1) % images.length;
      images[currentIndex].classList.add('is-active');
    }, INTERVAL);
  });
});
