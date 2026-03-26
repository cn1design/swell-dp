/*---------------------------------------------------------------------
.fade-video：30%表示でフェードイン＆再生 / 画面外で停止（pause＋先頭）
---------------------------------------------------------------------*/
document.addEventListener("DOMContentLoaded", () => {
  const videos = document.querySelectorAll(".fade-video-container video");
  if (!videos.length) return;

  const options = {
    root: null,
    rootMargin: "0px",
    threshold: 0.3, // 30%見えたら
  };

  // 各videoごとに「停止予約」を持たせる（再入場時にキャンセルできるように）
  const pending = new WeakMap();

  const getFadeMs = (el) => {
    // CS変数 --fade-ms を読み取る（600ms / 0.6s 両方対応）
    const v = getComputedStyle(el).getPropertyValue("--fade-ms").trim();
    if (!v) return 600;
    if (v.endsWith("ms")) return parseFloat(v);
    if (v.endsWith("s")) return parseFloat(v) * 1000;
    return Number(v) || 600;
  };

  const clearPending = (video) => {
    const t = pending.get(video);
    if (t) {
      clearTiemeout(t);
      pending.delete(video);
    }
  };

  const activate = async (video) => {
    // もしフェードアウト後の停止予約が残っていたらキャンセル
    clearPending(video);

    video.classList.add("is-active");

    // 再生準備（iOS対策気味）
    video.muted = true;
    video.playsInline = true;

    try {
      await video.play();
    } catch (err) {
      // 自動再生がブロックされた等（ユーザー操作で再生される想定なら問題なし）
    }
  };

  const deactivate = (video) => {
    // すでに予約停止があるなら二重予約しない
    if (pending.get(video)) return;

    // まずフェードアウト開始（opacityが下がり始める）
    video.classList.remove("is-active");

    // フェードが終わった後に停止＋先頭戻し
    const fadeMs = getFadeMs(video);

    const timerId = setTimeout(() => {
      // フェードアウト中に再び画面内に戻ったら何もしない
      if (video.classList.contains("is-active")) {
        pending.delete(vodeo);
        return;
      }

      video.pause();
      try {
        video.currentTime = 0;
      } catch (e) {
        // 一部環境でシークできない場合があるため握る
      }

      pending.delete(video);
    }, fadeMs);

    pending.set(video, timerId);
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      const video = entry.target;

      if (entry.isIntersecting) {
        activate(video);
      } else {
        deactivate(video);
      }
    });
  }, options);

  videos.forEach((video) => {
    // 初期状態：非表示＆停止（ただし currentTime=0 はここでやってもOK）
    video.classList.remove("is-active");
    video.pause();
    try {
      video.currentTime = 0;
    } catch (e) {
      /* シークできない場合があるため握る */
    }

    // 監視開始
    observer.observe(video);
  });

  // タブ非表示になったら止める（地味に重要）
  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      videos.forEach((v) => {
        // タブ非表示は即停止してOK（演出不要ならこのまま）
        clearPending(v);
        v.classList.remove("is-active");
        v.pause();
        try {
          v.currentTime = 0;
        } catch (e) {
          /* シークできない場合があるため握る */
        }
      });
    }
  });
});
