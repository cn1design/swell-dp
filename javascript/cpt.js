document.addEventListener("DOMContentLoaded", () => {
  const btn = document.querySelector(".dp-copy-btn");
  if (!btn) return;

  const textEl = btn.querySelector(".dp-copy-text");
  const defaultLabel = textEl ? textEl.textContent : "ブロックコードをコピーする";

  function getCodeToCopy() {
    const cfg = window.dpConfig || {};
    // 管理者: localStorage のトグル状態を優先（初期値 ON = インラインあり）
    if (cfg.isAdmin) {
      const mode = localStorage.getItem("dp_inline_mode");
      const useInline = mode !== null ? mode === "on" : true;
      return useInline ? btn.dataset.codeInline : btn.dataset.codeClean;
    }
    // ログイン済み = 有料会員 → クリーン版（インラインなし）
    if (cfg.isLoggedIn) {
      return btn.dataset.codeClean;
    }
    // 未ログイン = 無料ユーザー → インライン込み
    return btn.dataset.codeInline;
  }

  btn.addEventListener("click", () => {
    const code = getCodeToCopy();
    if (!code) return;
    navigator.clipboard.writeText(code).then(() => {
      btn.classList.add("is-copied");
      if (textEl) textEl.textContent = "コピー完了 ✓";
      setTimeout(() => {
        btn.classList.remove("is-copied");
        if (textEl) textEl.textContent = defaultLabel;
      }, 2500);
    });
  });
});
