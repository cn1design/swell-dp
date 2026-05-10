# dp-h1-image-heading-block 開発ログ

> 作成日：2026-05-08

---

## セッション一覧

---

### 2026-05-08 — 動作確認完了・v0.1.0 リリース

**結果:** バグなし・動作快適。本番配布zipをDropboxに保存済み。
**ステータス:** ✅ リリース完了

---

### 2026-05-08 — 初期実装完了（MVP v0.1.0）

**実装内容:**
- `@wordpress/create-block` でスキャフォールド生成
- block.json: `supports.align: ['left', 'center', 'right']` を設定。viewScript は不要のため削除。
- edit.js: 画像未選択時はPlaceholder表示、選択後はWYSIWYGプレビュー（h1+img）。設定UIはすべてInspectorControls内（掟3遵守）。
- save.js: `<h1><img></h1>` の静的HTML出力。InnerBlocks不使用のため、通常のReact要素を返す（掟1・2の適用外）。
- style.scss: SWELL等テーマのH1スタイルを `!important` で完全リセット。擬似要素（::before, ::after）も content:none で消去。img は display:block でlineHeight底部余白除去。
- editor.scss: フロントエンドと見た目を一致させるためのWYSIWYGリセット。

**技術的判断:**
- expanding-grid-block と異なり、InnerBlocks・render.php・view.js が不要なシンプル構成
- `display: block` を img に適用し margin auto でアライメント制御（vertical-align:bottom方式は不採用）
- `supports.align` でWordPress標準のalignleft/center/rightクラスを利用

**ビルド結果:**
- index.js: 3.61 KiB（エディタースクリプト）
- style-index.css: 959 bytes（フロントエンドCSS）
- index.css: 413 bytes（エディタースタイル）
- webpack compiled successfully in 621ms
