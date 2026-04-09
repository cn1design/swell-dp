/**
 * LPビルダー（カート式一括コピー）Phase 2-4
 * - チェックボックス ON/OFF ↔ カートリスト連動
 * - SortableJS によるドラッグ＆ドロップ並び替え
 * - 各アイテム個別削除・全解除・バッジ・ドロワー開閉
 * - 一括コピー：<style><script>抽出 → HTML結合 → CSS/JS集約 → クリップボード
 * - ESCキー対応・iOS scrollロック・トースト通知
 */
(function () {
  "use strict";

  // ページに dp-cart-trigger が存在しなければ何もしない
  const trigger = document.getElementById("dp-cart-trigger");
  if (!trigger) return;

  /* =========================================================
   * 状態管理（localStorage）
   * キー: 'dp_lp_cart' / 値: { postId, title, thumb }[] の JSON
   * archive ↔ standard ページをまたいでも状態を維持する
   * ========================================================= */
  const STORAGE_KEY = "dp_lp_cart";

  function loadCart() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    } catch (e) {
      return [];
    }
  }

  function saveCart() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
  }

  let cart = loadCart();

  /* =========================================================
   * DOM参照
   * ========================================================= */
  const drawer = document.getElementById("dp-cart-drawer");
  const overlay = document.getElementById("dp-cart-overlay");
  const closeBtn = document.getElementById("dp-cart-close");
  const cartList = document.getElementById("dp-cart-list");
  const cartEmpty = document.getElementById("dp-cart-empty");
  const cartBadge = document.getElementById("dp-cart-badge");
  const copyBtn = document.getElementById("dp-cart-copy-btn");
  const triggerCount = document.getElementById("dp-cart-trigger-count");

  // トリガー表示同期関数（closeDrawer から参照するため外部スコープに置く）
  let triggerSync = null;

  /* =========================================================
   * ドロワー開閉
   * iOS Safari では overflow:hidden が効かないため
   * position:fixed + scrollY 保存で背面スクロールを封じる
   * ========================================================= */
  let scrollY = 0;

  function openDrawer() {
    scrollY = window.scrollY;
    document.body.style.position = "fixed";
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = "100%";
    document.body.classList.add("dp-cart-is-open"); // 競合固定要素の制御用
    drawer.classList.add("is-open");
    drawer.setAttribute("aria-hidden", "false");
    overlay.classList.add("is-open");
    // ドロワーの最初のフォーカス可能な要素にフォーカス
    const firstFocusable = drawer.querySelector("button, [tabindex]");
    if (firstFocusable) firstFocusable.focus();
  }

  function closeDrawer() {
    document.body.style.position = "";
    document.body.style.top = "";
    document.body.style.width = "";
    document.body.classList.remove("dp-cart-is-open");
    window.scrollTo(0, scrollY);
    drawer.classList.remove("is-open");
    drawer.setAttribute("aria-hidden", "true");
    overlay.classList.remove("is-open");
    trigger.focus(); // フォーカスをトリガーに戻す
    // scroll event が発火しないケースに備えて明示的に再評価
    requestAnimationFrame(function () {
      if (triggerSync) triggerSync();
    });
  }

  trigger.addEventListener("click", openDrawer);
  closeBtn.addEventListener("click", closeDrawer);
  overlay.addEventListener("click", closeDrawer);

  // ESC キーでドロワーを閉じる
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && drawer.classList.contains("is-open")) {
      closeDrawer();
    }
  });

  /* =========================================================
   * カートUI更新
   * ========================================================= */
  function renderCart() {
    const count = cart.length;

    // バッジ
    if (cartBadge) cartBadge.textContent = count;
    if (triggerCount) {
      triggerCount.textContent = count;
      triggerCount.dataset.count = count;
    }
    trigger.setAttribute("aria-label", `LPビルダーを開く（選択: ${count}件）`);

    // 一括コピーボタン
    copyBtn.disabled = count === 0;

    // 全解除ボタン（カートが空のとき非表示）
    let clearBtn = document.getElementById("dp-cart-clear-btn");
    if (!clearBtn) {
      clearBtn = document.createElement("button");
      clearBtn.id = "dp-cart-clear-btn";
      clearBtn.className = "dp-cart-clear-btn";
      clearBtn.textContent = "全解除";
      clearBtn.addEventListener("click", function () {
        cart = [];
        saveCart();
        renderCart();
      });
      // ×ボタンの隣に挿入
      closeBtn.parentNode.insertBefore(clearBtn, closeBtn);
    }
    clearBtn.style.display = count === 0 ? "none" : "";

    // 空メッセージ
    cartEmpty.style.display = count === 0 ? "" : "none";

    // リスト描画
    cartList.innerHTML = "";
    cart.forEach((item) => {
      const li = document.createElement("li");
      li.className = "dp-cart-item";
      li.dataset.postId = item.postId;
      li.innerHTML = `
                <span class="dp-cart-item__drag" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                        <path d="M11 18c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-2-8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6 4c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                </span>
                ${
                  item.thumb
                    ? `<img class="dp-cart-item__thumb" src="${item.thumb}" alt="${escHtml(item.title)}" loading="lazy">`
                    : `<span class="dp-cart-item__thumb dp-cart-item__thumb--empty"></span>`
                }
                <span class="dp-cart-item__title">${escHtml(item.title)}</span>
                <button class="dp-cart-item__remove" data-post-id="${item.postId}" aria-label="${escHtml(item.title)}を削除">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            `;
      cartList.appendChild(li);
    });

    // SortableJS 再初期化
    initSortable();

    // チェックボックス同期（他ページ遷移後も含め全ページで整合）
    syncCheckboxes();
  }

  function escHtml(str) {
    return str
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  /* =========================================================
   * チェックボックス同期（カート配列 → DOM）
   * ========================================================= */
  function syncCheckboxes() {
    document.querySelectorAll(".pl-card-select__input").forEach((cb) => {
      const id = cb.dataset.postId;
      const inCart = cart.some((item) => item.postId === id);
      cb.checked = inCart;
      // カードハイライト
      const card = cb.closest(".pl-card");
      if (card) card.classList.toggle("is-selected", inCart);
    });
  }

  /* =========================================================
   * カートへの追加・削除
   * ========================================================= */
  function addToCart(postId, title, thumb, blockCode) {
    if (cart.some((item) => item.postId === postId)) return;
    cart.push({ postId, title, thumb, blockCode: blockCode || "" });
    saveCart();
    renderCart();
  }

  function removeFromCart(postId) {
    cart = cart.filter((item) => item.postId !== postId);
    saveCart();
    renderCart();
  }

  /* =========================================================
   * チェックボックス イベント（Event Delegation）
   * ========================================================= */
  document.addEventListener("change", function (e) {
    const cb = e.target.closest(".pl-card-select__input");
    if (!cb) return;

    const postId = cb.dataset.postId;
    const title = cb.dataset.title || "";
    const thumb = cb.dataset.thumb || "";

    if (cb.checked) {
      // カード内のコピーボタンからブロックコードを取得
      const card = cb.closest(".pl-card");
      const cpBtn = card
        ? card.querySelector(".pl-btn--copy:not(.is-disabled)")
        : null;
      const blockCode = cpBtn ? cpBtn.dataset.code : "";
      addToCart(postId, title, thumb, blockCode);
    } else {
      removeFromCart(postId);
    }
  });

  /* =========================================================
   * 削除ボタン（Event Delegation）
   * ========================================================= */
  cartList.addEventListener("click", function (e) {
    const btn = e.target.closest(".dp-cart-item__remove");
    if (!btn) return;
    removeFromCart(btn.dataset.postId);
  });

  /* =========================================================
   * SortableJS 並び替え
   * ========================================================= */
  let sortableInstance = null;

  function initSortable() {
    if (sortableInstance) {
      sortableInstance.destroy();
      sortableInstance = null;
    }
    if (typeof Sortable === "undefined" || cartList.children.length < 2) return;

    sortableInstance = Sortable.create(cartList, {
      animation: 150,
      handle: ".dp-cart-item__drag",
      ghostClass: "dp-cart-item--ghost",
      onEnd: function () {
        // DOM順にcart配列を更新
        const newOrder = [];
        cartList.querySelectorAll(".dp-cart-item").forEach((li) => {
          const found = cart.find((item) => item.postId === li.dataset.postId);
          if (found) newOrder.push(found);
        });
        cart = newOrder;
        saveCart();
        // バッジ等の更新（リスト再描画は不要）
        const count = cart.length;
        if (cartBadge) cartBadge.textContent = count;
        if (triggerCount) {
          triggerCount.textContent = count;
          triggerCount.dataset.count = count;
        }
      },
    });
  }

  /* =========================================================
   * リストビュー用ラベル注入
   * 先頭の Gutenberg ブロックコメントに metadata.name を追加する。
   * ブレースを数えてネスト JSON を正しく抽出・再構築する。
   * ========================================================= */
  function injectBlockLabel(html, label) {
    // 最初の <!-- wp:blockname [attrs] --> を検出
    const m = html.match(/<!--\s*wp:\S+\s*/);
    if (!m) return html;

    const prefixEnd = m.index + m[0].length;
    const rest = html.slice(prefixEnd);

    if (rest[0] === "{") {
      // JSON 属性あり: ブレース対応で終端を見つける
      let depth = 0,
        end = -1;
      for (let i = 0; i < rest.length; i++) {
        if (rest[i] === "{") depth++;
        else if (rest[i] === "}" && --depth === 0) {
          end = i;
          break;
        }
      }
      if (end === -1) return html;
      try {
        const attrs = JSON.parse(rest.slice(0, end + 1));
        if (!attrs.metadata) attrs.metadata = {};
        attrs.metadata.name = label;
        return (
          html.slice(0, prefixEnd) + JSON.stringify(attrs) + rest.slice(end + 1)
        );
      } catch (e) {
        return html; // パース失敗時はそのまま
      }
    } else {
      // JSON 属性なし (<!-- wp:html -->): {} を新規挿入
      const injected = JSON.stringify({ metadata: { name: label } });
      return html.slice(0, prefixEnd) + injected + " " + rest;
    }
  }

  /* =========================================================
   * 一括コピー（Phase 3 コアロジック）
   *
   * 処理順序（順序を守ること）:
   *   Step 1. <style> を非貪欲マッチで抽出・タグごと除去
   *   Step 2. style 除去後に空になった <!-- wp:html --> ブロックを除去
   *           ※空白文字・改行のみが残ったものが対象
   *   Step 3. 連続する3行以上の改行を \n\n に正規化
   *   Step 4. 各パターン HTML を \n\n で結合
   *   Step 5. 集約 CSS を末尾の単一 <!-- wp:html --> ブロックに追加
   * ========================================================= */
  function bulkCopy() {
    if (cart.length === 0) return;

    const allCss = [];
    const allJs = [];
    const allHtml = [];

    cart.forEach((item) => {
      if (!item.blockCode) return;

      let code = item.blockCode;

      // Step 1a: <style> 抽出・除去（非貪欲マッチ）
      code = code.replace(
        /<style[^>]*>([\s\S]*?)<\/style>/gi,
        (_, cssContent) => {
          const trimmed = cssContent.trim();
          if (trimmed) allCss.push(trimmed);
          return "";
        },
      );

      // Step 1b: <script> も抽出・除去し末尾ブロックに集約
      //          途中の「カスタム HTML」をリストビューから排除するため
      code = code.replace(
        /<script[^>]*>([\s\S]*?)<\/script>/gi,
        (_, jsContent) => {
          const trimmed = jsContent.trim();
          if (trimmed) allJs.push(trimmed);
          return "";
        },
      );

      // Step 2: 空になった <!-- wp:html --> ブロックを完全削除
      //         空白・改行のみが残ったものを対象とする
      code = code.replace(
        /<!--\s*wp:html\s*-->(\s*)<!--\s*\/wp:html\s*-->/gi,
        "",
      );

      // Step 3: 連続3行以上の改行を \n\n に正規化 → 前後トリム
      code = code.replace(/\n{3,}/g, "\n\n").trim();

      // Step 4: 先頭ブロックにパターン名を注入（Gutenberg リストビュー用）
      code = injectBlockLabel(code, item.title);

      if (code) allHtml.push(code);
    });

    if (allHtml.length === 0) {
      showToast("コピーできるブロックコードがありません", true);
      return;
    }

    // Step 5: ブロック間を \n\n で結合（Gutenberg パース必須条件）
    let output = allHtml.join("\n\n");

    // Step 6: CSS・JS を末尾の単一 <!-- wp:html --> ブロックに集約
    //         <style> → <script> の順で格納し、ラベルで内容を明示
    if (allCss.length > 0 || allJs.length > 0) {
      let inner = "";
      if (allCss.length > 0)
        inner += "<style>\n" + allCss.join("\n") + "\n</style>";
      if (allJs.length > 0)
        inner +=
          (inner ? "\n" : "") + "<script>\n" + allJs.join("\n") + "\n</script>";

      const hasJs = allJs.length > 0;
      const hasCss = allCss.length > 0;
      const label =
        hasJs && hasCss
          ? "専用CSS・JS | さわらない"
          : hasJs
            ? "専用JS | さわらない"
            : "専用CSS | さわらない";

      const block = "<!-- wp:html -->\n" + inner + "\n<!-- /wp:html -->";
      output += "\n\n" + injectBlockLabel(block, label);
    }

    navigator.clipboard
      .writeText(output)
      .then(() => {
        closeDrawer();
        showToast("一括コピー完了 ✓  SWELLエディタに貼り付けてください");
      })
      .catch(() => {
        showToast(
          "コピーに失敗しました。ブラウザの設定をご確認ください。",
          true,
        );
      });
  }

  copyBtn.addEventListener("click", bulkCopy);

  /* =========================================================
   * トースト通知
   * ========================================================= */
  function showToast(message, isError = false) {
    const existing = document.getElementById("dp-cart-toast");
    if (existing) existing.remove();

    const toast = document.createElement("div");
    toast.id = "dp-cart-toast";
    toast.className = "dp-cart-toast" + (isError ? " is-error" : "");
    toast.textContent = message;
    document.body.appendChild(toast);

    // アニメーション用に次フレームで表示クラス付与
    requestAnimationFrame(() => {
      requestAnimationFrame(() => toast.classList.add("is-visible"));
    });

    setTimeout(() => {
      toast.classList.remove("is-visible");
      toast.addEventListener("transitionend", () => toast.remove(), {
        once: true,
      });
    }, 3000);
  }

  /* =========================================================
   * トリガーのスクロール表示制御
   * SWELLは html[data-scrolled="true/false"] でスクロール状態を管理する。
   * .p-fixBtnWrap のクラス/スタイルは変化しないため監視対象にしない。
   * document.documentElement の data-scrolled 変化を直接監視する。
   * ========================================================= */
  (function () {
    const html = document.documentElement;

    function sync() {
      const scrolled = html.dataset.scrolled === "true";
      trigger.classList.toggle("is-visible", scrolled);
    }

    triggerSync = sync;

    new MutationObserver(sync).observe(html, {
      attributes: true,
      attributeFilter: ["data-scrolled"],
    });

    sync(); // 初期状態チェック
  })();

  /* =========================================================
   * 初期レンダリング
   * ========================================================= */
  renderCart();
})();
