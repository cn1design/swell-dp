/**
 * dp-tutorial.js
 * スプラッシュ画面 + ステップ・バイ・ステップ チュートリアル + ヘルプボタン
 *
 * 技術的注意事項（仕様書 §5 参照）:
 *  - scrollIntoView 後 500ms 待機してから getBoundingClientRect() を取得
 *  - ハイライトは clip-path: path(evenodd) 中抜き（overflow:hidden の影響を受けない）
 *  - Step 4: transitionend で drawer アニメーション完了を待機
 */
(function () {
  "use strict";

  // ページに #dp-splash が存在しなければ何もしない
  const splash = document.getElementById("dp-splash");
  if (!splash) return;

  /* =========================================================
   * 定数
   * ========================================================= */
  const STORAGE_KEY = "dp_tutorial_done";
  const TOTAL_STEPS = 4;
  const DRAWER_ANIM_MS = 400; // drawer transition + バッファ

  const STEP_TEXTS = [
    "まずはベースを整える『共通CSS』をコピーして、エディタの一番上に貼り付けます。",
    "使いたいデザインが見つかったら、チェックを入れて追加します。",
    "選んだパーツはここから確認できます。",
    "順番を入れ替えて『一括コピー』を押し、エディタに貼り付ければ完成です！",
  ];

  /* =========================================================
   * 状態
   * ========================================================= */
  let currentStep = 0;
  let dummyInjected = false;
  let highlightedEls = [];
  let tutorialOverlay = null;
  let tutorialTooltip = null;
  let spotlight = null;
  let gifModal = null;

  /* =========================================================
   * ステップのターゲット要素取得
   * ========================================================= */
  function getTarget(stepIndex) {
    switch (stepIndex) {
      case 0:
        return document.querySelector(".dp-base-css-wrap");
      case 1:
        // カード全体をハイライト（チェックボックスより視認性が高い）
        return document.querySelector(".pl-card");
      case 2:
        return document.getElementById("dp-cart-trigger");
      case 3:
        return document.getElementById("dp-cart-copy-btn");
      default:
        return null;
    }
  }

  function getDirection(stepIndex) {
    // bottom, bottom, top, top
    return ["bottom", "bottom", "top", "top"][stepIndex] || "bottom";
  }

  /* =========================================================
   * スプラッシュ画面
   * ========================================================= */
  function initSplash() {
    if (localStorage.getItem(STORAGE_KEY)) {
      // 完了済み → スプラッシュを即削除
      splash.remove();
      return;
    }

    // 「さっそく使ってみる」
    const startBtn = document.getElementById("dp-splash-start");
    if (startBtn) {
      startBtn.addEventListener("click", function () {
        hideSplash(function () {
          startTutorial();
        });
      });
    }

    // 「スキップして使い始める」
    const skipBtn = document.getElementById("dp-splash-skip");
    if (skipBtn) {
      skipBtn.addEventListener("click", function () {
        hideSplash(function () {
          doneTutorial();
        });
      });
    }
  }

  function hideSplash(callback) {
    splash.classList.add("is-hiding");
    splash.addEventListener(
      "transitionend",
      function () {
        splash.remove();
        if (callback) callback();
      },
      { once: true },
    );
  }

  /* =========================================================
   * チュートリアル 開始・終了
   * ========================================================= */
  function startTutorial() {
    // チュートリアル実行中フラグ（#dp-cart-trigger 強制表示などCSSで参照）
    document.body.classList.add("is-tutorial-running");

    // オーバーレイ生成
    tutorialOverlay = document.createElement("div");
    tutorialOverlay.id = "dp-tutorial-overlay";
    tutorialOverlay.className = "dp-tutorial-overlay";
    document.body.appendChild(tutorialOverlay);

    // ツールチップ生成
    tutorialTooltip = document.createElement("div");
    tutorialTooltip.id = "dp-tutorial-tooltip";
    tutorialTooltip.className = "dp-tutorial-tooltip";
    tutorialTooltip.innerHTML = [
      '<button class="dp-tutorial-tooltip__gif-btn" hidden>',
      '  <svg viewBox="0 0 24 24" fill="currentColor">',
      '    <path d="M8 5v14l11-7z"/>',
      "  </svg>",
      "  使い方を動画で見る",
      "</button>",
      '<p class="dp-tutorial-tooltip__text"></p>',
      '<div class="dp-tutorial-tooltip__footer">',
      '  <span class="dp-tutorial-tooltip__step"></span>',
      '  <div class="dp-tutorial-tooltip__actions">',
      '    <button class="dp-tutorial-tooltip__skip">スキップ</button>',
      '    <button class="dp-tutorial-tooltip__next">次へ</button>',
      "  </div>",
      "</div>",
    ].join("\n");
    document.body.appendChild(tutorialTooltip);

    tutorialTooltip
      .querySelector(".dp-tutorial-tooltip__next")
      .addEventListener("click", nextStep);
    tutorialTooltip
      .querySelector(".dp-tutorial-tooltip__skip")
      .addEventListener("click", function () {
        doneTutorial();
      });
    tutorialTooltip
      .querySelector(".dp-tutorial-tooltip__gif-btn")
      .addEventListener("click", function () {
        openGifModal();
      });

    goToStep(0);
  }

  function doneTutorial() {
    localStorage.setItem(STORAGE_KEY, "1");
    cleanupTutorialUI();
  }

  function nextStep() {
    if (currentStep >= TOTAL_STEPS - 1) {
      doneTutorial();
      return;
    }
    goToStep(currentStep + 1);
  }

  function cleanupTutorialUI() {
    clearHighlight();

    // Step 4 で開いたドロワーを閉じる
    cleanupStep4();

    closeGifModal();

    if (tutorialOverlay) {
      tutorialOverlay.remove();
      tutorialOverlay = null;
    }
    if (tutorialTooltip) {
      tutorialTooltip.remove();
      tutorialTooltip = null;
    }

    // チュートリアル実行中フラグを解除（トリガーボタンを通常状態に戻す）
    document.body.classList.remove("is-tutorial-running");
  }

  /* =========================================================
   * ステップ移動
   * ========================================================= */
  function goToStep(stepIndex) {
    currentStep = stepIndex;
    clearHighlight();

    if (stepIndex === 3) {
      showStep4();
    } else {
      showStep(stepIndex);
    }
  }

  function showStep(stepIndex) {
    const target = getTarget(stepIndex);

    if (!target) {
      // ターゲットがないページ（standard ページなど）はスキップ
      if (stepIndex < TOTAL_STEPS - 1) {
        goToStep(stepIndex + 1);
      } else {
        doneTutorial();
      }
      return;
    }

    // Step 3（#dp-cart-trigger）は position:fixed のためスクロール不要
    if (stepIndex === 2) {
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          highlight(target);
          renderTooltip(stepIndex, target, false);
        });
      });
      return;
    }

    // それ以外のステップ: ビューポートに収めてから位置計算
    // behavior:"instant" を使うことで getBoundingClientRect() が
    // スクロール完了後の正確なビューポート座標を返す（smooth だと長距離時にズレる）
    target.scrollIntoView({ behavior: "instant", block: "center" });

    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        highlight(target);
        renderTooltip(stepIndex, target, false);
      });
    });
  }

  /* =========================================================
   * Step 4: ドロワーを自動展開してからツールチップを表示
   * ========================================================= */
  function showStep4() {
    const drawer = document.getElementById("dp-cart-drawer");
    if (!drawer) {
      doneTutorial();
      return;
    }

    // ドロワーが既に開いていない場合は開く
    if (!drawer.classList.contains("is-open")) {
      // dp-cart.js の API を使う（§5-3 注意: アニメーション待機が必要）
      if (window.dpCart && window.dpCart.openDrawer) {
        window.dpCart.openDrawer();
      } else {
        // フォールバック: trigger をクリック
        const trigger = document.getElementById("dp-cart-trigger");
        if (trigger) {
          trigger.dispatchEvent(new MouseEvent("click", { bubbles: true }));
        }
      }

      // transitionend でアニメーション完了を待機（§5-3）
      let settled = false;
      function onTransitionEnd(e) {
        if (e.propertyName !== "transform") return;
        if (settled) return;
        settled = true;
        drawer.removeEventListener("transitionend", onTransitionEnd);
        afterDrawerOpen(drawer);
      }
      drawer.addEventListener("transitionend", onTransitionEnd);

      // フォールバック: transitionend が発火しない場合
      setTimeout(function () {
        if (settled) return;
        settled = true;
        drawer.removeEventListener("transitionend", onTransitionEnd);
        afterDrawerOpen(drawer);
      }, DRAWER_ANIM_MS + 150);
    } else {
      afterDrawerOpen(drawer);
    }
  }

  function afterDrawerOpen(drawer) {
    // カートが空ならダミーアイテムを注入
    const cartList = document.getElementById("dp-cart-list");
    const cartEmpty = document.getElementById("dp-cart-empty");
    const isEmpty =
      !cartList ||
      cartList.children.length === 0 ||
      (cartEmpty && cartEmpty.style.display !== "none");

    if (isEmpty && cartList) {
      injectDummyItem(cartList, cartEmpty);
    }

    // コピーボタンの disabled を一時解除
    const copyBtn = document.getElementById("dp-cart-copy-btn");
    if (copyBtn && copyBtn.disabled) {
      copyBtn.disabled = false;
      copyBtn.dataset.tutorialDisabled = "1";
    }

    // Step 4: スポットライトはドロワー全体に合わせる
    // コピーボタンは outline でハイライト（CSS 定義済み）
    if (copyBtn) {
      createSpotlight(drawer);
      copyBtn.classList.add("is-tutorial-highlight");
      highlightedEls.push(copyBtn);
      renderTooltip(3, copyBtn, true);
    }

    // GIF チュートリアルモーダルを自動表示（ツールチップ描画後）
    requestAnimationFrame(function () {
      openGifModal();
    });
  }

  function injectDummyItem(cartList, cartEmpty) {
    dummyInjected = true;
    const li = document.createElement("li");
    li.className = "dp-cart-item dp-cart-item--tutorial-dummy";
    li.innerHTML = [
      '<span class="dp-cart-item__drag" aria-hidden="true">',
      '  <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">',
      '    <path d="M11 18c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-2-8c-1.1 0-2',
      "    .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6",
      "    4c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9",
      '    2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>',
      "  </svg>",
      "</span>",
      '<span class="dp-cart-item__thumb dp-cart-item__thumb--empty"></span>',
      '<span class="dp-cart-item__title">ヒーローセクション（例）</span>',
    ].join("\n");
    cartList.appendChild(li);
    if (cartEmpty) cartEmpty.style.display = "none";
  }

  function cleanupStep4() {
    // ダミーアイテム削除
    if (dummyInjected) {
      const dummy = document.querySelector(".dp-cart-item--tutorial-dummy");
      if (dummy) dummy.remove();
      dummyInjected = false;

      // 空メッセージ復元
      const cartList = document.getElementById("dp-cart-list");
      const cartEmpty = document.getElementById("dp-cart-empty");
      if (cartList && cartEmpty && cartList.children.length === 0) {
        cartEmpty.style.display = "";
      }
    }

    // コピーボタンを再 disabled
    const copyBtn = document.getElementById("dp-cart-copy-btn");
    if (copyBtn && copyBtn.dataset.tutorialDisabled) {
      copyBtn.disabled = true;
      delete copyBtn.dataset.tutorialDisabled;
    }

    // ドロワーを閉じる
    const drawer = document.getElementById("dp-cart-drawer");
    if (drawer) {
      if (drawer.classList.contains("is-open")) {
        if (window.dpCart && window.dpCart.closeDrawer) {
          window.dpCart.closeDrawer();
        } else {
          const closeBtn = document.getElementById("dp-cart-close");
          if (closeBtn) closeBtn.click();
        }
      }
    }
  }

  /* =========================================================
   * スポットライト
   *
   * 【構成】
   *  1. #dp-tutorial-spotlight（position:fixed）: ターゲット rect に重なる枠線/グロー
   *  2. .dp-tutorial-overlay の clip-path: evenodd ルールで矩形を中抜き
   *     外側パス（全画面・時計回り）+ 内側パス（スポットライト・時計回り）を
   *     evenodd で描くと、内側領域が透明（穴）になる
   *
   * これにより overflow:hidden の親の影響を受けず、
   * z-index の昇格なしにターゲットが本物の透明で見える。
   * ========================================================= */
  var SPOTLIGHT_PAD = 8; // ターゲット周囲の余白 (px)
  var SPOTLIGHT_R   = 8; // 角丸半径（#dp-tutorial-spotlight の border-radius と合わせる）

  function createSpotlight(target) {
    clearSpotlight();
    var rect = target.getBoundingClientRect();
    var pad  = SPOTLIGHT_PAD;
    var x    = rect.left   - pad;
    var y    = rect.top    - pad;
    var w    = rect.width  + pad * 2;
    var h    = rect.height + pad * 2;
    var r    = SPOTLIGHT_R;

    // 1. 枠線/グロー div
    var el = document.createElement("div");
    el.id = "dp-tutorial-spotlight";
    el.style.top    = y + "px";
    el.style.left   = x + "px";
    el.style.width  = w + "px";
    el.style.height = h + "px";
    document.body.appendChild(el);
    spotlight = el;

    // 2. オーバーレイを clip-path: path(evenodd) で中抜き
    if (tutorialOverlay) {
      var vw = window.innerWidth;
      var vh = window.innerHeight;
      // 外側: 全画面矩形（時計回り）
      var outer = "M 0 0 H " + vw + " V " + vh + " H 0 Z";
      // 内側: 角丸矩形（時計回り） → evenodd で穴になる
      var inner =
        "M " + (x + r) + " " + y +
        " H " + (x + w - r) +
        " A " + r + " " + r + " 0 0 1 " + (x + w) + " " + (y + r) +
        " V " + (y + h - r) +
        " A " + r + " " + r + " 0 0 1 " + (x + w - r) + " " + (y + h) +
        " H " + (x + r) +
        " A " + r + " " + r + " 0 0 1 " + x + " " + (y + h - r) +
        " V " + (y + r) +
        " A " + r + " " + r + " 0 0 1 " + (x + r) + " " + y + " Z";
      tutorialOverlay.style.clipPath = "path(evenodd, '" + outer + " " + inner + "')";
    }
  }

  function clearSpotlight() {
    if (spotlight) {
      spotlight.remove();
      spotlight = null;
    }
    if (tutorialOverlay) {
      tutorialOverlay.style.clipPath = "";
    }
  }

  /* =========================================================
   * GIF チュートリアルモーダル
   * ========================================================= */
  var GIF_SRC = ""; // GIF ファイルが決まったら URL を入れる
  var GIF_STEPS = [
    "共通CSSにチェックを入れ、LPビルダーに追加します",
    "使いたいデザインパターンにチェックを入れて追加します",
    "LPビルダーで順番を整えたら「一括コピー」を押します",
    "SWELLエディタに貼り付けてプレビューを確認したら完成です",
  ];

  function openGifModal() {
    closeGifModal(); // 二重生成防止

    var modal = document.createElement("div");
    modal.className = "dp-gif-modal";
    modal.setAttribute("role", "dialog");
    modal.setAttribute("aria-modal", "true");
    modal.setAttribute("aria-label", "使い方チュートリアル動画");

    var stepsHtml = GIF_STEPS.map(function (text, i) {
      return (
        '<li class="dp-gif-modal__step">' +
        '<span class="dp-gif-modal__step-num">' + (i + 1) + "</span>" +
        "<span>" + text + "</span>" +
        "</li>"
      );
    }).join("\n");

    var mediaHtml = GIF_SRC
      ? '<img src="' + GIF_SRC + '" alt="使い方チュートリアル">'
      : '<span class="dp-gif-modal__media--placeholder">GIF 動画を準備中</span>';

    modal.innerHTML =
      '<div class="dp-gif-modal__inner">' +
      '<button class="dp-gif-modal__close" aria-label="閉じる">' +
      '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>' +
      "</button>" +
      '<div class="dp-gif-modal__media">' + mediaHtml + "</div>" +
      '<div class="dp-gif-modal__body">' +
      '<p class="dp-gif-modal__lead">使い方の流れ</p>' +
      '<ul class="dp-gif-modal__steps">' + stepsHtml + "</ul>" +
      "</div>" +
      "</div>";

    document.body.appendChild(modal);
    gifModal = modal;

    // オーバーレイクリックで閉じる
    modal.addEventListener("click", function (e) {
      if (e.target === modal) closeGifModal();
    });
    modal.querySelector(".dp-gif-modal__close").addEventListener("click", closeGifModal);

    // フェードイン
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        modal.classList.add("is-visible");
      });
    });
  }

  function closeGifModal() {
    if (!gifModal) return;
    var modal = gifModal;
    gifModal = null;
    modal.classList.remove("is-visible");
    modal.addEventListener(
      "transitionend",
      function () { modal.remove(); },
      { once: true }
    );
  }

  /* =========================================================
   * ハイライト
   * ========================================================= */
  function highlight(el) {
    el.classList.add("is-tutorial-highlight");
    highlightedEls.push(el);
    createSpotlight(el);
  }

  function clearHighlight() {
    highlightedEls.forEach(function (el) {
      el.classList.remove("is-tutorial-highlight");
    });
    highlightedEls = [];
    clearSpotlight();
  }

  /* =========================================================
   * ツールチップ描画・位置計算
   * ========================================================= */
  function renderTooltip(stepIndex, target, showGif) {
    if (!tutorialTooltip) return;

    // コンテンツ更新
    tutorialTooltip.querySelector(".dp-tutorial-tooltip__text").textContent =
      STEP_TEXTS[stepIndex];
    tutorialTooltip.querySelector(".dp-tutorial-tooltip__step").textContent =
      stepIndex + 1 + " / " + TOTAL_STEPS;
    tutorialTooltip.querySelector(".dp-tutorial-tooltip__next").textContent =
      stepIndex === TOTAL_STEPS - 1 ? "はじめる" : "次へ";

    const gifBtn = tutorialTooltip.querySelector(".dp-tutorial-tooltip__gif-btn");
    if (gifBtn) gifBtn.hidden = !showGif;

    // 一旦非表示で DOM に置いてサイズ計測
    tutorialTooltip.classList.remove("is-visible");
    tutorialTooltip.style.top = "";
    tutorialTooltip.style.left = "";
    tutorialTooltip.style.bottom = "";
    tutorialTooltip.style.transform = "";

    // rAF でレイアウト確定後に位置計算
    requestAnimationFrame(function () {
      positionTooltip(target, getDirection(stepIndex), stepIndex);
      requestAnimationFrame(function () {
        tutorialTooltip.classList.add("is-visible");
      });
    });
  }

  function positionTooltip(target, direction, stepIndex) {
    if (!tutorialTooltip || !target) return;

    const vpW = window.innerWidth;
    const MARGIN = 16;

    // SP: 画面下部固定（CSS で上書き済みだが JS でも適用）
    if (vpW <= 959) {
      tutorialTooltip.style.bottom = MARGIN + "px";
      tutorialTooltip.style.left = "50%";
      tutorialTooltip.style.transform = "translateX(-50%)";
      return;
    }

    // Step 3: トリガーボタン（right:112px 固定）の真上に right 基準で配置
    if (stepIndex === 2) {
      const tRect = target.getBoundingClientRect();
      const tipH = tutorialTooltip.offsetHeight;
      tutorialTooltip.style.right = "280px";
      tutorialTooltip.style.top = Math.max(MARGIN, tRect.top - tipH - MARGIN) + "px";
      tutorialTooltip.style.left = "";
      tutorialTooltip.style.bottom = "";
      tutorialTooltip.style.transform = "";
      return;
    }

    const tRect = target.getBoundingClientRect();
    const tipW = tutorialTooltip.offsetWidth;
    const tipH = tutorialTooltip.offsetHeight;
    const vpH = window.innerHeight;

    let top, left;

    switch (direction) {
      case "top":
        top = tRect.top - tipH - MARGIN;
        left = tRect.left + tRect.width / 2 - tipW / 2;
        // 上に収まらなければ下にフォールバック
        if (top < MARGIN) {
          top = tRect.bottom + MARGIN;
        }
        break;
      case "right":
        top = tRect.top + tRect.height / 2 - tipH / 2;
        left = tRect.right + MARGIN;
        // 右に収まらなければ左にフォールバック
        if (left + tipW > vpW - MARGIN) {
          left = tRect.left - tipW - MARGIN;
        }
        break;
      case "bottom":
      default:
        top = tRect.bottom + MARGIN;
        left = tRect.left + tRect.width / 2 - tipW / 2;
        // 下に収まらなければ上にフォールバック
        if (top + tipH > vpH - MARGIN) {
          top = tRect.top - tipH - MARGIN;
        }
        break;
    }

    // 画面端補正
    left = Math.max(MARGIN, Math.min(left, vpW - tipW - MARGIN));
    top = Math.max(MARGIN, Math.min(top, vpH - tipH - MARGIN));

    tutorialTooltip.style.top = top + "px";
    tutorialTooltip.style.left = left + "px";
    tutorialTooltip.style.bottom = "";
    tutorialTooltip.style.transform = "";
  }

  /* =========================================================
   * ヘルプボタン
   * ========================================================= */
  function initHelpButton() {
    const helpBtn = document.getElementById("dp-help-btn");
    if (!helpBtn) return;

    helpBtn.addEventListener("click", function () {
      // 既存のチュートリアルUIがあれば一旦クリア
      if (tutorialOverlay || tutorialTooltip) {
        cleanupTutorialUI();
      }
      startTutorial();
    });
  }

  /* =========================================================
   * 初期化
   * ========================================================= */
  function init() {
    initSplash();
    initHelpButton();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
