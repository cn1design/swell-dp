#DP実装

- プラグイン名: DP Flexible Inner Container (テーマ非依存Gutenbergブロック)
- クラス名プレフィックス: `dp-block-flex-container`
- 目的: LPで必須となる「背景色による縁取り＋角丸のインナーボックス」をノーコードで柔軟に構築できるラッパーブロック。

## 実装要件 (React & Gutenberg API)

### 1. ブロック構造

- **Outer Wrapper (親):** 全幅（Full）または幅広（Wide）に対応し、外側の背景色（例：緑）を持つ。
- **Inner Container (子):** コンテンツ領域。背景色（例：白）を持ち、`max-width` で中央配置される。
- 子コンテナ内に `<InnerBlocks />` を配置し、画像やテキストブロックを自由に挿入可能にする。

### 2. InspectorControls (専用調整UIの構築)

Gutenberg標準の `supports` に頼らず、`@wordpress/components` の `RangeControl` 等を使用して、以下の専用設定パネルを作成すること。

- **外側背景色 (Outer Background):** `ColorPalette` を使用。
- **内側背景色 (Inner Background):** `ColorPalette` を使用（初期値：白）。
- **外側パディング (フチの太さ):** `RangeControl` (0〜100px)。親コンテナの `padding` として機能させ、緑色の縁の太さを調整する。初期値: 32px。
- **内側角丸 (Border Radius):** `RangeControl` (0〜50px)。子コンテナの `border-radius` として機能させる。初期値: 16px。
- **最小の高さ (Min Height):** `RangeControl` または数値入力。親コンテナの `min-height` に適用。

### 3. スタイルとWYSIWYG (CSS & React)

- エディター上でのスライダー操作が、インラインスタイルまたはCSS変数を通じてリアルタイムにプレビュー（WYSIWYG）に反映されるように構築すること。
- テーマ（SWELL等）の既存CSSに影響されないよう、クラス名は完全にカプセル化すること。
- スマホ閲覧時（レスポンシブ）は、フチの太さ（外側パディング）が大きすぎてコンテンツ領域を圧迫しないよう、CSS側で `max-width` や `padding` のフォールバック（例：SP時はpaddingを半分にする等）をよしなに考慮すること。

---

# 回答

設計分析と提案、素晴らしいです。動的ブロック（render.php）の採用、CSS変数による制御、SP時のpadding自動縮小のアイデア、すべてその方針で進めてください。

懸念点の「Full Width対応」について：

- **「Full Width（全幅）対応」は【必須（必要）】です。**
- LPの各セクションの背景（外側の色）をブラウザの画面幅いっぱい（100vw）に広げて配置する用途がメインとなるためです。
- エディター内でWYSIWYGが崩れないよう、`editor.scss` に必要な追加調整（フルワイド用のマージン相殺など）を行って、エディターと本番環境の見た目を一致させてください。

追加要望（MVP仕様として統合）：

- インナーコンテナ（内側のコンテンツ領域）に「ドロップシャドウ（影）を付ける」ためのトグル（ToggleControl）をInspectorControlsに追加してください（初期値はONで影あり）。
- 影のCSSは `box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);` などを目安に、LPに合うリッチな質感を表現してください。

以上の仕様で、開発をスタートしてください！

---

#追加要件

追加の重要な技術要件です。
Gutenberg標準の `supports.align: ['wide', 'full']` を有効にする際、SWELLのような従来型テーマでは、記事本文のラッパー（max-width）に閉じ込められてしまい、真のフルワイドになりません。

これを防ぐため、ブロックに `.alignfull` が付与された場合は、テーマの枠を強制的に突破して画面幅（100vw）に広がる「ブレイクアウトCSS」をプラグイン側で必ず実装してください。

【参考：SWELLのフルワイド突破ロジック】

```css
left: calc(50% - 50vw + var(--swl-scrollbar_width, 0px) / 2) !important;
width: calc(100vw - var(--swl-scrollbar_width, 0px)) !important;
position: relative;

追加の機能要件です。LPのセクションごとに最適なコンテンツ幅が異なるため、インナーコンテナの「最大幅」をエディターで自由に変更できるようにします。

【追加要件：インナーコンテンツ最大幅の調整UI】
1. InspectorControlsへの追加:
   - 「インナー最大幅 (Max Width)」を調整できるUIを追加してください。
   - `RangeControl`（例: 400px 〜 1600px、ステップ50px）や数値入力で指定できるようにすること。
   - 初期値はLPで汎用的な `1200px` （または `1000px`）としてください。

2. CSS設計 (レスポンシブの担保):
   - エディターで指定した値をCSS変数（例: `--inner-max-width`）としてインナーコンテナに渡してください。
   - インナーコンテナのCSSは、単に `max-width: var(--inner-max-width);` とするのではなく、スマホ等で画面幅を突き破らないよう、以下のようなモダンな記述を用いて堅牢に実装してください。
     例：`max-width: min(var(--inner-max-width, 1200px), 100%);` （※左右のパディングと競合しないよう適切に計算すること）
   - コンテンツは常に中央配置（`margin-inline: auto;` 等）されるようにしてください。
```

---

#DP実装修正指示：インナーコンテナの3層構造化とレスポンシブ余白

表示確認をした結果、LPのレイアウトとして構造的なアップデートが必要です。以下の3点を修正してください。

1. HTML構造の「3層化」
   現在の2層構造から、コンテンツ幅を制御する要素を分離した3層構造に変更してください。

- 1層目 [Outer]: `.dp-block-flex-container` (ブレイクアウトして画面幅100vwに広がる緑の背景)
- 2層目 [Inner]: `.dp-block-flex-container__inner` (白などの全面背景色＋角丸＋左右パディングを持つ)
- 3層目 [Content]: `.dp-block-flex-container__content` (ここに最大幅を持たせ、`<InnerBlocks />` を配置する)

2. CSS設計の変更

- `__inner` ではなく、新しい `__content` に対して `max-width: min(var(--fic-inner-max-width), 100%);` と `margin-inline: auto;` を適用し、コンテンツを中央配置してください。
- これにより、「広い画面では白背景（**inner）は親の枠まで広がりつつ、中のコンテンツ（**content）だけが1200pxの中央に収まる」という視覚効果を実現します。

3. レスポンシブ左右パディング（UI追加）

- `__inner` に対する左右の余白（padding-left / padding-right）を、エディターで詳細に調整できるUIを追加してください。
- `@wordpress/components` の `__experimentalBoxControl` またはそれに準ずるUI（PC / タブレット / スマホ ごとに値を設定でき、単位 px / em を切り替えられるもの）を導入してください。
- 画面幅が1024pxなどで縮小された際、コンテンツがブラウザの縁にピッタリくっつくのを防ぐための重要な機能です。

---

## 追加要件（実装済み）

### 4. `__inner` に `position: relative` を追加

```css
.dp-block-flex-container__inner {
  position: relative;
}
```

最外郭の枠内にぴったり収まる仕様とするため。

---

### 5. `min-height` → レスポンシブ `height`（PC/タブレット/SP）

`minHeight`（単一値）を廃止し、ブレイクポイントごとに高さを設定できるUIに変更。

**属性:**

- `heightPc`（integer, default: 0）
- `heightTablet`（integer, default: 0）
- `heightSp`（integer, default: 0）

**CSS変数:** `--fic-height-pc` / `--fic-height-tb` / `--fic-height-sp`（0 = auto）

**CSS設計:** `__inner` と `__content` に `height: 100%` を付与し、外側の高さに追従させる。

**UI:** 「高さ（レスポンシブ）」パネル（初期値は閉じた状態）。0 = auto の補足説明を表示。

---

### 6. `__content` をフレックスコンテナ化（縦中央・左寄せ）

コンテンツを縦中央・左寄せで配置するため、`__content` にフレックスを適用。

```css
.dp-block-flex-container__content {
  display: flex;
  align-items: center;
  justify-content: flex-start;
}
```

---

### 7. `__inner::before` にグラデーションオーバーレイ追加

画像スライダーの左端をぼかすため、擬似要素で全面グラデーションオーバーレイを追加。

**仕様:**

- 左端〜（65% - 80px）: `--fic-inner-bg` 100% 不透明
- （65% - 80px）〜 65%: 80px かけて透明にフェード
- 65%〜右端: 透明（画像の左端がぼかされる）

```css
.dp-block-flex-container__inner::before {
  content: "";
  position: absolute;
  inset: 0;
  z-index: 2;
  background: linear-gradient(
    to right,
    var(--fic-inner-bg) 0,
    var(--fic-inner-bg) calc(65% - 80px),
    transparent 65%
  );
  pointer-events: none;
}
```

**エディター対応:** `editor.scss` にて `::before { display: none; }` を追加し、エディター内ではオーバーレイを非表示にする（スライダーが隠れるのを防ぐ）。

---

# DP実装 追加修正指示

現在開発中の2つのプラグインに対し、レスポンシブ対応と運用UXを向上させるためのアップデートを行います。以下の修正を実装してください。

## 1. DP Flexible Inner Container (`dp-flexible-inner-container`) の修正

- **要件:** 高さ（Min Height）の指定において、pxなどの数値だけでなく「`auto`」を設定できるようにしてください。
- **実装詳細:**
  - InspectorControlsの高さ指定UI（RangeControlやTextControl等）において、ユーザーが明示的に `auto` を選択・入力できるようにするか、空欄時に `auto` が適用される仕様にしてください。
  - コンテンツ量に応じてフレキシブルにコンテナが伸縮する状態をデフォルト（初期値）とし、CSS出力が `min-height: auto;` として正しく機能するように調整してください。

## 2. DP H1 Image Headline Block (`dp-h1-image-heading-block`) の修正

- **要件:** エディター画面での画像サイズ（Width / Height）指定において、px指定に加えて「`%`」での指定を追加してください。
- **実装詳細:**
  - InspectorControlsのサイズ入力UIをアップデートし、単位（px, %）をドロップダウン等で切り替えられるようにするか、`@wordpress/components` の `UnitControl`（またはそれに準ずる単位付き入力UI）を採用してください。
  - 入力された単位付きの文字列（例: `80%` や `400px`）が、フロントエンドおよびエディター上の `<img>` タグの `width` / `height` スタイル（属性ではなくインラインスタイル等）として正しく反映され、WYSIWYGが保たれるように設計してください。

---

# DP実装修正指示：インナーコンテンツ最大幅のレスポンシブ化

`dp-flexible-inner-container` プラグインをアップデートし、`.dp-block-flex-container__content` の最大幅を PC と SP（959px以下）で個別に設定できるようにしてください。

## 1. 属性（Attributes）の更新

- `block.json` を修正し、以下の属性構成に変更してください。
  - `innerMaxWidthPC`: integer (default: 1200)
  - `innerMaxWidthSP`: integer (default: 600) ※初期値はよしなに調整可

## 2. InspectorControls (UI) の修正

- PC用とSP用の最大幅を個別に設定できる `RangeControl` を追加してください。
- 可能であれば、Gutenberg標準のデバイス切り替えコンポーネント（PC/スマホアイコン）を使用して、直感的に切り替えられるUIにしてください。
- 単位は引き続き `px` とし、スライダー範囲は 300px 〜 1600px 程度に設定してください。

## 3. レンダリングとCSSの修正 (render.php & style.scss)

- `render.php` で、2つの値を個別のCSS変数として出力してください。
  - `--fic-inner-max-width-pc`
  - `--fic-inner-max-width-sp`
- `style.scss` にて、以下のメディアクエリを用いて制御してください。
  - デフォルト（PC）: `max-width: min(var(--fic-inner-max-width-pc), 100%);`
  - `@media (max-width: 959px)`: `max-width: min(var(--fic-inner-max-width-sp), 100%);`
- エディター画面（edit.js / editor.scss）でも、選択中のプレビューモード（PC/SP）に合わせて見た目が正しく反映されるよう（WYSIWYG）に調整してください。
