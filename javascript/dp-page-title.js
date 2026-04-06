/*---------------------------------------------------------------------
 * SWELL限定｜固定ページのサブタイトルの「–」と前後の半角スペース削除
 * 「–」はSWELL親テーマのPHPでテキストノードとして直接出力されるためJS対応のみ有効
 * CSSで visibility: hidden → JS除去後に visible に切替（チラつき防止）
 * body に .dp-ttl--{a|b|c|d} が付与されているページで読み込まれる
 ---------------------------------------------------------------------*/
document.addEventListener("DOMContentLoaded", () => {
  const element = document.querySelector(".c-pageTitle__subTitle");
  if (!element) return;

  element.textContent = element.textContent
    .replace(/–\s*/g, "")
    .replace(/\s*–/g, "")
    .trim();

  element.style.visibility = "visible";
});
