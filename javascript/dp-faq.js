(function () {
  'use strict';

  function initFaq(faqEl) {
    if (faqEl.dataset.dpFaqInit) return; // 二重初期化防止
    faqEl.dataset.dpFaqInit = '1';

    const items = Array.from(faqEl.querySelectorAll('.swell-block-faq__item'));
    items.forEach(function (item) {
      const q = item.querySelector('.faq_q');
      if (!q) return;
      q.addEventListener('click', function () {
        const isOpen = item.classList.contains('is-open');
        items.forEach(function (i) { i.classList.remove('is-open'); });
        if (!isOpen) item.classList.add('is-open');
      });
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    // dp-faq--b（ボーダー区切り）と dp-faq--c（白カード）の両バリアント対応
    document.querySelectorAll('.dp-faq--b .swell-block-faq, .dp-faq--c .swell-block-faq').forEach(initFaq);
  });
})();
