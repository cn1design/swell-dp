document.addEventListener("DOMContentLoaded", function () {
  const section = document.querySelector(
    ".dp-media-text-section--sticky-fade-image",
  );
  if (!section) return;

  const gallery = section.querySelector("#fadeGallery");
  const stickyEl = section.querySelector("#fadeStickyInner");
  const dotsWrap = section.querySelector("#fadeDots");
  const textBlock = section.querySelector(".text-block");
  if (!gallery || !stickyEl || !dotsWrap || !textBlock) return;

  const isMobile = () => window.innerWidth <= 768;
  const figures = stickyEl.querySelectorAll("figure");
  const TOTAL = figures.length;

  figures.forEach((f) => f.classList.remove("active"));

  if (dotsWrap.querySelectorAll("span").length === 0) {
    figures.forEach(() => dotsWrap.appendChild(document.createElement("span")));
  }
  const dots = dotsWrap.querySelectorAll("span");
  dots.forEach((d) => d.classList.remove("active"));

  // ★ 各画像の表示比率（合計10）
  const WEIGHTS = [1.5, 7, 1.5];
  const TOTAL_W = WEIGHTS.reduce((a, b) => a + b, 0); // 10
  // 各画像の開始・終了しきい値を事前計算
  const thresholds = WEIGHTS.reduce((acc, w, i) => {
    const start = i === 0 ? 0 : acc[i - 1].end;
    acc.push({ start, end: start + w / TOTAL_W });
    return acc;
  }, []);
  // [{ start:0, end:0.15 }, { start:0.15, end:0.85 }, { start:0.85, end:1 }]

  let currentIndex = -1;

  function syncHeight() {
    gallery.style.height = isMobile() ? "" : textBlock.offsetHeight + "px";
  }

  function activate(index) {
    if (index === currentIndex) return;
    currentIndex = index;
    figures.forEach((f, i) => f.classList.toggle("active", i === index));
    dots.forEach((d, i) => d.classList.toggle("active", i === index));
  }

  function getIndexFromProgress(progress) {
    // progressがどのしきい値区間に入るか判定
    for (let i = 0; i < thresholds.length; i++) {
      if (progress < thresholds[i].end) return i;
    }
    return TOTAL - 1;
  }

  function onScroll() {
    if (isMobile()) return;

    const rect = textBlock.getBoundingClientRect();
    const total = textBlock.offsetHeight - window.innerHeight;

    let progress;
    if (total <= 0) {
      const scrollRange = textBlock.offsetHeight + window.innerHeight;
      progress = Math.max(
        0,
        Math.min(1, (-rect.top + window.innerHeight) / scrollRange),
      );
    } else {
      progress = Math.max(0, Math.min(1, -rect.top / total));
    }

    activate(getIndexFromProgress(progress));
  }

  syncHeight();
  if (!isMobile()) activate(0);
  onScroll();

  window.addEventListener("scroll", onScroll, { passive: true });
  window.addEventListener("resize", () => {
    syncHeight();
    if (!isMobile() && currentIndex === -1) activate(0);
  });
});
