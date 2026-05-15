# カスタムブロックプラグイン開発：Expanding Grid Block

> 最終更新：2026-04-15
> ステータス：実装待ち（設計確定済み・ネスト対応追記）

---

## プロジェクト概要

SWELLやCocoonなど、テーマに依存せずに動作するWordPressの独立したカスタムブロックプラグインを開発する。
機能は、非エンジニアでも直感的に作成できる「Expanding Grid（クリックでその行の下に100%幅でコンテンツが展開するUI）」のブロック。

---

## 設計方針（実装前に確定した6つの決定）

| 決定項目         | 選択                                                       | 理由                                                                  |
| ---------------- | ---------------------------------------------------------- | --------------------------------------------------------------------- |
| パネルのDOM配置  | 行末アイテムの直後にJSで注入                               | CSS展開だとグリッド全体がリフローして崩れる                           |
| パネルの内容保存 | 各アイテムのDOM内に非表示で保持                            | SEO・アクセシビリティ・JS失敗時のフォールバック確保                   |
| 行検出方式       | `offsetTop` の差が±2px以内を同一行と判定                   | サブピクセルレンダリング差の吸収に必要                                |
| リサイズ監視     | `ResizeObserver` でグリッドコンテナを監視                  | `window.resize` は過剰発火・コンテナ幅変化を検出できない              |
| レンダリング方式 | PHP `render_callback`（動的レンダリング）                  | 構造変更時のブロックバリデーションエラーを回避。HTMLをPHP側で完全制御 |
| カラム数         | 親ブロックのattribute（デフォルト4）でエディタから変更可能 | ハードコード禁止                                                      |

---

## ファイル構成（プラグイン）

```
wp-content/plugins/expanding-grid-block/
├── expanding-grid-block.php    # プラグインエントリ + render_callback 登録
├── package.json
├── build/                      # npm run build 後に生成
└── src/
    ├── expanding-grid-wrapper/ # 親ブロック
    │   ├── block.json
    │   ├── edit.js
    │   ├── save.js             # return null（動的レンダリングのため）
    │   └── editor.scss
    ├── expanding-grid-item/    # 子ブロック
    │   ├── block.json
    │   ├── edit.js
    │   └── save.js             # <InnerBlocks.Content /> のみ返す
    ├── view.js                 # フロントエンドJS（行検出・パネル注入）
    └── style.scss              # フロントエンドCSS
```

---

## ブロック構造

### 親ブロック：`expanding-grid-wrapper`

**block.json attributes:**

```json
{
  "columns": { "type": "integer", "default": 4 },
  "gap": { "type": "string", "default": "16px" }
}
```

**allowedBlocks:** `["expanding-grid-block/expanding-grid-item"]` のみ許可（ネスト対応のため `expanding-grid-wrapper` 自体は `expanding-grid-item` の InnerBlocks 側で許可する）。

**save.js:** `return null;`（PHP側でレンダリング）

**PHP render_callback が出力するHTML構造:**

```html
<div
  class="eg-wrapper"
  data-columns="4"
  style="
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
  "
>
  <!-- expanding-grid-item × N が直接の子 -->
  <!-- JSが注入する eg-panel も直接の子に入る（grid-column: 1 / -1）-->
</div>
```

---

### 子ブロック：`expanding-grid-item`

**block.json attributes:**

```json
{
  "title": { "type": "string", "source": "html", "selector": ".eg-item__title" }
}
```

**allowedBlocks（ネスト対応）:**
`expanding-grid-item` の InnerBlocks では制限なし（`allowedBlocks` を指定しない）とし、
`core/table`・`core/paragraph` に加えて `expanding-grid-block/expanding-grid-wrapper` の挿入を許可する。
これにより「メーカー → シリーズ → 料金テーブル」の多段ネストが実現できる。

**save.js が出力するHTML構造（InnerBlocks.Content を含む）:**

```html
<div class="eg-item" data-index="{{index}}">
  <!-- タイトル（クリック可能なボタン） -->
  <button
    class="eg-item__title"
    aria-expanded="false"
    aria-controls="eg-panel-{{index}}"
  >
    {{RichTextのタイトル}}
  </button>

  <!-- パネルコンテンツ（初期は非表示。JSが読み取り・移動する） -->
  <div class="eg-item__content" id="eg-panel-{{index}}" hidden>
    <InnerBlocks.Content />
  </div>
</div>
```

> **ポイント:** `eg-item__content` は初期状態で `hidden` 属性。JSが読み取ってパネルに注入するため、元のDOMにはSEO・アクセシビリティ用として残す。

---

## ネスト（入れ子）対応

### ユースケース

```
[親] eg-wrapper（メーカー: iPhone / iPad / Xperia）
  └── [eg-panel が開く]
        └── [子] eg-wrapper（シリーズ: iPhone 16 / 15 Pro / ...）
              └── [eg-panel が開く]
                    └── core/table（料金テーブル）
```

### 解決すべき2つの技術課題

#### 課題1：JSのイベントスコープ汚染

`eg-wrapper` ごとにクリックリスナーを登録すると、子の `eg-item__title` クリックが親の `eg-wrapper` にもバブリングしてしまう。

**解決策：`closest` によるスコープチェック（`stopPropagation` は使わない）**

```js
// 各 eg-wrapper ごとに initGrid() を呼ぶ
function initGrid(wrapper) {
  wrapper.addEventListener("click", function (e) {
    const titleBtn = e.target.closest(".eg-item__title");
    if (!titleBtn) return;

    // このボタンが「このwrapper」の直接の子 eg-item のものか確認
    const item = titleBtn.closest(".eg-item");
    const itemWrapper = item.closest(".eg-wrapper");
    if (itemWrapper !== wrapper) return; // ← ネストされた子グリッドのクリックは無視

    // 以降は通常の行検出・パネル注入処理
    handleItemClick(wrapper, item);
  });
}

// 全 eg-wrapper に対して initGrid() を呼ぶ
document.querySelectorAll(".eg-wrapper").forEach(initGrid);
```

> `stopPropagation()` を使うと子グリッドのクリックが親まで届かなくなり、
> 親グリッドが「パネル外クリック → 全閉じ」などの処理を実装したときに干渉する。
> 代わりに `itemWrapper !== wrapper` の早期リターンでスコープを制御する。

#### 課題2：親パネルの `max-height` が子展開後に不足する

親パネルを開いた時点の `scrollHeight` を `max-height` にセットすると、
その後に子グリッドが展開して高さが増えても親パネルがクリップされてしまう。

**解決策：開閉アニメーション完了後に `max-height` を `none` に解放する**

```js
function openPanel(panel) {
  panel.style.maxHeight = panel.scrollHeight + "px"; // アニメーション開始
  panel.classList.add("is-open");

  // transitionend 後に max-height: none へ切り替え
  // → 子パネルが展開しても親パネルが自然に追従する
  panel.addEventListener(
    "transitionend",
    function onEnd() {
      panel.style.maxHeight = "none";
      panel.removeEventListener("transitionend", onEnd);
    },
    { once: true },
  );
}

function closePanel(panel) {
  // max-height: none の状態から閉じる際は、
  // いったん現在の実際の高さをセットしてからアニメーション開始
  panel.style.maxHeight = panel.scrollHeight + "px";
  requestAnimationFrame(() => {
    panel.style.maxHeight = "0";
    panel.classList.remove("is-open");
  });
}
```

### ネスト時のCSS：子グリッドは縦積みアコーディオン表示

#### data-depth 属性（PHPが出力）

PHP `render_callback` はネストの深さを `data-depth` 属性として出力する。
CSSはこの属性を起点にスタイルをカプセル化し、**子セレクタ `>` で1階層のみに適用する**。

```php
// render_callback 内でネスト深さを追跡
function eg_render_wrapper($attrs, $content, $block) {
    $depth = isset($block->context['eg/depth']) ? (int)$block->context['eg/depth'] : 0;
    // ...
    return '<div class="eg-wrapper" data-depth="' . $depth . '" ...>';
}
```

#### CSSスコープ設計

**原則: 各セレクタは `data-depth` または `>` 子セレクタで1階層のみに適用する。**
子孫セレクタ（スペース結合）はスコープが広すぎるため使用しない。

```css
/* ─────────────────────────────────────────────
   最上位グリッド (data-depth="0")
   grid-template-columns は PHP が inline style で出力する
───────────────────────────────────────────── */
.eg-wrapper[data-depth="0"] {
  display: grid;
}

/* 最上位グリッドの直接の子アイテムのみ対象 */
.eg-wrapper[data-depth="0"] > .eg-item > .eg-item__title {
  /* 最上位タイトルのスタイル（ピル型など） */
  border-radius: 8px;
  padding: 14px 20px;
  width: 100%;
  text-align: left;
  background: var(--color_main_thin, #f0f0f0);
  font-weight: bold;
}

/* ─────────────────────────────────────────────
   第1階層ネストグリッド (data-depth="1")
   eg-panel の直接の子として展開される縦積みアコーディオン
───────────────────────────────────────────── */
.eg-wrapper[data-depth="1"] {
  display: flex;
  flex-direction: column;
  gap: 4px;
  /* PHP の inline style（grid-template-columns）を上書き */
  grid-template-columns: unset !important;
}

/* 第1階層タイトルボタン */
.eg-wrapper[data-depth="1"] > .eg-item > .eg-item__title {
  padding: 10px 16px;
  font-size: 0.9em;
  border-radius: 4px;
  background: var(--color_gray, #f0f0f0);
  width: 100%;
  text-align: left;
}

/* 第1階層のパネル: flexコンテナ内なので grid-column 不要 */
.eg-wrapper[data-depth="1"] > .eg-panel {
  grid-column: unset;
}

/* ─────────────────────────────────────────────
   第2階層以降（data-depth="2"〜）
   第1階層と同じ縦積みだが、さらにインデントを付ける
───────────────────────────────────────────── */
.eg-wrapper[data-depth="2"] {
  display: flex;
  flex-direction: column;
  gap: 2px;
  grid-template-columns: unset !important;
  padding-left: 12px;
  border-left: 2px solid var(--color_border, #ddd);
}

.eg-wrapper[data-depth="2"] > .eg-item > .eg-item__title {
  padding: 8px 12px;
  font-size: 0.85em;
  border-radius: 4px;
  background: transparent;
  border: 1px solid var(--color_border, #ddd);
  width: 100%;
  text-align: left;
}
```

#### SP（スマホ）フォールバック

SP では全グリッドが1列になるため、ネストが深くなると横幅が窮屋になりやすい。
**インデント幅を削減し、タイトルの縦方向のパディングを優先して確保する。**

````css
@media screen and (max-width: 599px) {
  /* 最上位グリッド: SP では1列（CSSで列数を上書き） */
  .eg-wrapper[data-depth="0"] {
    grid-template-columns: 1fr !important;
  }

  /* 最上位タイトル: SPでもタップしやすいサイズ維持 */
  .eg-wrapper[data-depth="0"] > .eg-item > .eg-item__title {
    padding: 12px 16px;
  }

  /* 第1階層: インデントなし（横幅節約） */
  .eg-wrapper[data-depth="1"] {
    gap: 2px;
  }

  .eg-wrapper[data-depth="1"] > .eg-item > .eg-item__title {
    padding: 10px 12px;
  }

  /* 第2階層以降: border-left のインデント幅を削減 */
  .eg-wrapper[data-depth="2"] {
    padding-left: 8px; /* PC: 12px → SP: 8px */
  }

  .eg-wrapper[data-depth="2"] > .eg-item > .eg-item__title {
    padding: 8px 10px;
    font-size: 0.82em;
  }
}

### 行検出のスコープ制限

`initGrid(wrapper)` 内で行を検出する際、**このwrapperの直接の子 `eg-item` のみ**を対象にする。
ネストされた子グリッドの `eg-item` は行検出に含めない。

```js
// NG: ネストされた eg-item も含まれてしまう
const items = wrapper.querySelectorAll('.eg-item');

// OK: このwrapperの直接の子のみ（ネストした子グリッドの eg-item は除外）
const items = Array.from(wrapper.querySelectorAll('.eg-item')).filter(
  item => item.closest('.eg-wrapper') === wrapper
);
````

---

## フロントエンドJS（view.js）の仕様

### データ構造のイメージ

```
eg-wrapper
├── eg-item[0]  eg-item[1]  eg-item[2]  eg-item[3]  ← 行1
├── [eg-panel が行1末尾の直後に注入される]
├── eg-item[4]  eg-item[5]  eg-item[6]  eg-item[7]  ← 行2
└── [eg-panel が行2末尾の直後に注入される]
```

### 行検出アルゴリズム

```
1. 全 eg-item の offsetTop を取得
2. offsetTop の差が ±2px 以内のアイテムを「同一行」としてグループ化
3. 各行グループの末尾アイテムを「行末アイテム」として記録
```

### クリック時の挙動

```
1. クリックされた eg-item が属する行を特定
2. すでに開いている eg-panel があれば閉じる（排他制御）
   - eg-panel.remove() でDOMから削除
   - 元の eg-item__content を hidden に戻す
3. クリックされたアイテムと同じ行が開いていた場合 → 閉じるだけ（トグル）
4. 新しいパネルを開く:
   a. eg-panel div を生成（grid-column: 1 / -1 のCSS適用済み）
   b. 該当アイテムの eg-item__content の内容を eg-panel にコピー
   c. 行末アイテムの直後に insertAfter（DOM挿入）
   d. CSS transition で max-height: 0 → max-height: {実測値} にアニメーション
5. クリックされた eg-item__title の aria-expanded を true に更新
```

### リサイズ監視

```js
// ResizeObserver でグリッドコンテナを監視（window.resize は使用しない）
const ro = new ResizeObserver(() => {
  // 開いているパネルを閉じる（行構造が変わるため）
  closeAllPanels();
  // 行検出キャッシュをクリア（次回クリック時に再計算）
  rowCache = null;
});
ro.observe(gridWrapper);
```

### アニメーション仕様

```css
.eg-panel {
  grid-column: 1 / -1;
  overflow: hidden;
  max-height: 0;
  transition: max-height 0.3s ease;
}
.eg-panel.is-open {
  max-height: {JSで実測した scrollHeight を style に直接セット};
}
```

---

## エディター側のUX（edit.js）

- アニメーション・パネル展開は**一切実装しない**
- 縦積みの枠線付きボックスで「タイトル」「コンテンツ（InnerBlocks）」が確認できるシンプルなプレビューのみ
- 親ブロックのインスペクターパネルに **「カラム数（PC）」** のスライダーコントロールを配置

```
[Expanding Grid]
┌─────────────────────────┐
│ タイトルテキスト（編集可）│
├─────────────────────────┤
│ コンテンツ（InnerBlocks）│
│   ブロックを追加...      │
└─────────────────────────┘
[+ アイテムを追加]
```

---

## アクセシビリティ要件

- `aria-expanded="false/true"` をタイトルボタンに付与（JS制御）
- `aria-controls="eg-panel-{index}"` でボタンとパネルを紐付け
- パネルには `role="region"` と `aria-labelledby` を付与
- キーボード操作：Enter / Space でボタンを操作可能（`<button>` タグを使用することで自動対応）
- フォーカス管理：パネルオープン後は最初のフォーカス可能要素にフォーカス移動

---

## レスポンシブ対応

カラム数はCSSの `grid-template-columns` で制御。JS側はリサイズ時に再計算するため、列数の変更に自動対応。

| ブレークポイント         | グリッド列数                       |
| ------------------------ | ---------------------------------- |
| PC（960px以上）          | `data-columns` の値（デフォルト4） |
| タブレット（600〜959px） | 2列（CSS固定）                     |
| SP（599px以下）          | 1列（CSS固定）                     |

> タブレット・SPのカラム数はCSS側で上書きするため、JSの行検出は `ResizeObserver` で自動的に対応する。

---

## 作業手順（Claude Code実行ステップ）

### Step 1: プラグイン雛形の生成

```bash
cd /Users/d-hiyoshi/Local Sites/cndesign2026/app/public/wp-content/plugins
npx @wordpress/create-block@latest expanding-grid-block --no-plugin-header-fields
```

### Step 2: ブロック構造の実装

1. `src/` 配下に `expanding-grid-wrapper/` と `expanding-grid-item/` の2ブロック構成にリファクタリング
2. 各 `block.json` を上記仕様で作成
3. `expanding-grid-block.php` に `register_block_type` × 2 と `render_callback` を実装

### Step 3: フロントエンドJS + CSSの実装

1. `src/view.js` に上記アルゴリズムを実装
2. `src/style.scss` にグリッドレイアウトとパネルアニメーションCSSを実装

### Step 4: エディターUIの実装

1. `src/expanding-grid-wrapper/edit.js` にカラム数コントロールのインスペクターを実装
2. `src/expanding-grid-item/edit.js` に縦積みプレビューを実装

### Step 5: ビルド・動作確認

```bash
cd /Users/d-hiyoshi/Local Sites/cndesign2026/app/public/wp-content/plugins/expanding-grid-block
npm install && npm run build
```

その後、WordPress管理画面でプラグインを有効化してブロックエディターでの動作を確認する。

---

## 実装・修正時のダブルチェックルール

コードを変更した後、「動いた」と報告する前に必ず以下のシナリオを順番に手元で確認すること。

### フロントエンド動作チェックリスト

| # | 確認項目 | 合格条件 |
|---|---|---|
| 1 | 1階層目：単体の開閉 | クリックでスライド展開、再クリックで閉じる |
| 2 | 1階層目：排他制御 | 別アイテムをクリックすると前のパネルが閉じる |
| 3 | 1階層目：開いたまま維持 | パネルが開いた後、自動で閉じない（400ms以上放置）|
| 4 | **2階層目：1階層目が維持されるか** | 2階層目を開いても1階層目がリセットされない ← 最重要 |
| 5 | 2階層目：単体の開閉 | 2階層目も同様にスライド展開・排他制御が動く |
| 6 | ウィンドウ横幅リサイズ | 列数変化時にパネルが閉じる（ResizeObserver） |
| 7 | ウィンドウ縦リサイズ | 縦方向リサイズではパネルが閉じない |

### よくある誤爆パターン（既知の地雷）

- **ResizeObserver が高さ変化でも発火する**: 内側パネルが開くと外側 wrapper の高さが変わり、外側パネルが即閉じされる。修正は「横幅のみ監視」に限定すること。
- **`height: auto` への transition**: `transition: height` がアクティブな状態で `height: auto` をセットするとブラウザが `0` にスナップする。`transition: none` で無効化してから変更すること。
- **`state.transitioning` のタイマー孤立**: 新しいパネルを開く際に前のタイマーをキャンセルしないと、`transitioning = false` が早期に発火して ResizeObserver の誤爆を防げなくなる。必ず `clearTimeout(state.transitionTimer)` を呼ぶこと。

---

## 動的ブロック（render.php）実装ルール

### save.js の正しいパターン（InnerBlocks を持つ動的ブロック）

`render.php` を持つ動的ブロックで `InnerBlocks` を使用する場合、`save.js` はラッパー div を返してはならない。

**理由:** WordPress はブロックを「内側から外側へ」レンダリングする。`save.js` の div が `$content` に含まれ、`render.php` のラッパーと二重になる（`items found= 0` の原因）。

```jsx
// ✅ OK: InnerBlocks.Content のみ返す（render.php がラッパーを提供）
export default function save() {
    return <InnerBlocks.Content />;
}

// ❌ NG: ラッパー div を返すと $content に二重ラップされる
export default function save() {
    return (
        <div {...useBlockProps.save({ className: 'eg-wrapper' })}>
            <InnerBlocks.Content />
        </div>
    );
}
```

対応する render.php:
```php
// $content = 直接の子ブロックのレンダー結果（ラッパーなし）
echo '<div class="eg-wrapper" style="...">' . $content . '</div>';
```

### save.js で null を返してはならない

`return null` にすると WordPress がブロックを自己閉鎖タグ（`<!-- wp:block-name /-->`）でシリアライズし、`$content` が空文字になる。InnerBlocks の中身が完全に消失する。

| パターン | $content の中身 | 結果 |
|---|---|---|
| `return null` | 空文字 | フロントエンドが空 ❌ |
| `return <div><InnerBlocks.Content /></div>` | `<div>内容</div>` | 二重ラップ ❌ |
| `return <InnerBlocks.Content />` | 内容のみ | 正常 ✅ |

### DOM構造の確認方法（デバッグ手順）

`items found= 0` や空表示が発生したら、まず DOM を確認する:

```
期待する正常構造:
<div class="eg-wrapper dp-wrap">   ← render.php
  <div class="eg-item">            ← 直接の子
    <button class="eg-item__title">
    <div class="eg-item__content" hidden>

二重ラップのバグ構造:
<div class="eg-wrapper dp-wrap">   ← render.php
  <div class="eg-wrapper">         ← save.js の div（← ここが問題）
    <div class="eg-item">          ← getDirectItems が見つけられない
```

ブラウザ DevTools → Elements → `.eg-wrapper.dp-wrap` の直下に `.eg-item` が直接いるかを確認すること。
