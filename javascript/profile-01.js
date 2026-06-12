/*----------------------------------------------------------------------
 * プロフィール画像フェード（スクロール連動）
 *
 * - media-block（左列）の画像2枚をスクロール位置で切り替え
 * - テキストブロックの50%スクロール地点で1枚目→2枚目にフェード
 * - SP（959px以下）ではsticky解除・2枚縦並び（CSSで制御）
 ----------------------------------------------------------------------*/
document.addEventListener("DOMContentLoaded", () => {
  const section = document.querySelector(".dp-profile-section--a");
  if (!section) return;

  const textBlock = section.querySelector(".text-block");
  if (!textBlock) return;

  const isMobile = () => window.innerWidth <= 959;

  function onScroll() {
    if (isMobile()) {
      section.classList.remove("show-second");
      return;
    }

    const rect = textBlock.getBoundingClientRect();
    // text-block の90%地点がビューポート下端に達したら2枚目へ
    const point90 = rect.top + textBlock.offsetHeight * 0.9;
    section.classList.toggle("show-second", point90 <= window.innerHeight);
  }

  onScroll();
  window.addEventListener("scroll", onScroll, { passive: true });
  window.addEventListener("resize", onScroll);
});
