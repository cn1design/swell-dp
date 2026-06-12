# パターンB 切り替え後のレイアウトデザイン案

- CSS(SCSS)付与

#body_wrap のクラスにパターンA同様に専用クラスを追加する。

追加時に以下cssを適用させる。

```scss
// ヘッダーレイアウト アイキャッチ画像に被せる
#body_wrap:not(.single) .l-header {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
  transition: background-color 0.25s ease;
}

// タイトルエリア全体
.l-topTitleArea__body {
  text-shadow: none; // テキストシャドウ削除

  @media (min-width: 600px) {
    // 配置調整 上下均等
    margin-top: 3em !important;
  }
}

// タイトルエリア
.l-topTitleArea {
  @media (min-width: 600px) {
    // タイトルエリアの高さ広げ
    min-height: 360px;
  }
}

// タイトル
.c-pageTitle {
  display: flex;
  flex-direction: column;
  @media (min-width: 600px) {
    font-size: 3em;
  }
}

// サブタイトル
.c-pageTitle__subTitle {
  font-style: normal;
  margin-left: 0.2em;
  opacity: 1;
  top: 0;
  font-size: clamp(14px, 1.4vw, 16px);
}
```
