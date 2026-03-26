document.addEventListener("DOMContentLoaded", () => {
  const btn = document.querySelector(".dp-copy-btn");
  if (!btn) return;

  const textEl = btn.querySelector(".dp-copy-text");
  const defaultLabel = textEl ? textEl.textContent : "ブロックコードをコピーする";

  btn.addEventListener("click", () => {
    const code = btn.dataset.code;
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
