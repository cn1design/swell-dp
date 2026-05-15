#DP実装

- **プラグイン名:** DP H1 Image Headline Block (テーマ非依存Gutenbergブロック)
- **クラス名プレフィックス:** `dp-block-h1-image`
- **目的:** LPのヒーローセクション等で多用される、デザインされた画像（SVG等）を、SEOに強い「正しいH1タグ」として配置する。
- **ターゲット:** SWELLユーザーを含む、CSSを書かずに高品質なLPを作りたい全WordPressユーザー。

## 実装要件

### 1. HTMLセマンティクスの厳守

- **出力タグ:** 最外郭の出力タグは必ず `<h1>` とすること。内部に `<div>` やブロックレベル要素を含めてはならない。
- **内部構造:** `<h1>`タグの中に、`<img>`タグを配置するシンプルな構造。
- **出力例:** `<h1 class="wp-block-dp-h1-image {align_class}"><img src="..." alt="..." width="..." height="..."></h1>`

### 2. 管理者UXの最大化 (React実装)

- **画像選択UI:** ブロック配置時、画像未選択の場合は `@wordpress/components` の `Placeholder` を使用し、「画像をアップロード」ボタンを表示する。`@wordpress/block-editor` の `MediaUpload` コンポーネントを使用し、画像をアップロード・選択可能にする。
- **InspectorControls (右サイドバー) の設定:**
  - **Altテキスト (必須):** 画像選択後、InspectorControlsに「Altテキスト（代替テキスト）」入力フィールド（`TextControl`）を設け、**必須入力**とする。このテキストを `<img>` の `alt` 属性に反映させる。
  - **画像サイズ:** `width` と `height` を入力可能にする。初期値は選択した画像のオリジナルサイズ。
  - **アライメント:** 左・中央・右寄せを設定可能にし、`<h1>`タグにクラスを付与する。
- **WYSIWYG:** エディター上の見た目とフロントエンド（公開側）の見た目が完全に一致するようReact側を構築すること。

### 3. スタイル (CSS)

- **カプセル化:** 全CSSを独自のプレフィックス（.dp-block-h1-image）内にネストし、BEM風の命名規則を使用。
- **テーマ非依存設計:** WordPress標準の `theme.json` に依存せず、ブロック独自のCSSで完結させること。SWELL等の特定テーマに依存しない。
- **H1スタイルリセット:** テーマ（SWELL等）の既存のH1スタイル（下線、背景色、マージンなど）をリセットし、画像が意図した通りに表示されるための強力なリセットCSSを実装すること。
- **レスポンシブ:** スマホ閲覧時、画像が画面幅を超えずに縮小されるようなレスポンシブ対応を含めること。

## 技術的詳細

- **使用コンポーネント:** `@wordpress/block-editor` の `MediaUpload`, `Placeholder`, `InspectorControls`, `TextControl` および `@wordpress/block-editor` ツールバーコンポーネント。
- **アトリビュート:** `imageId` (ID), `imageUrl` (URL), `imageAlt` (Altテキスト), `imageWidth` (幅), `imageHeight` (高さ), `align` (配置).
- **block.json:** カテゴリは「text」または「layout」とし、サポート機能として「HTMLの編集」を無効化（SEOタグを破壊されないため）することを推奨。

---

Bで進めましょう。以下の技術詳細（CSSリセット方針とアライメント実装方法）を仕様に組み込んだ上で、そのまま `/wp-block-new dp-h1-image-heading-block` を実行し、開発を開始してください。

【追記する技術詳細】

1. H1スタイルの完全リセット（超重要）
   SWELL等の高機能テーマがH1に付与するあらゆる装飾を無効化するため、プラグイン側のCSS（フロントエンドおよびエディター内）で以下のような強力なリセットを行うこと。

- `margin`, `padding`, `border`, `background`, `box-shadow` を初期化。
- `::before`, `::after` の擬似要素を `content: none !important;` または `display: none !important;` で確実に消去。
- 画像下部の謎の余白（line-heightによる隙間）を防ぐため、H1の `line-height` や画像自体の `vertical-align`, `display: block;` の関係性を最適化すること。

2. アライメント（align）の実装方法

- Gutenbergの標準的な `supports: { align: ['left', 'center', 'right'] }` などを活用し、複雑なラッパーを作らずに `<h1>` タグ自体に `has-text-align-{alignment}` などの標準クラスが付与されるようにする。
- 内部の `<img>` は親であるH1のテキストアライメントに自然に従う設計にすること。

3. 画像のレスポンシブ

- `<img>` には `max-width: 100%; height: auto;` を適用し、エディターで指定したwidth/heightを保持しつつも、スマホ閲覧時にコンテナ幅を突き破らないようにすること。

仕様は以上です。実用最小限の完璧なMVP（最初のバージョン）を作り上げてください。開発スタート！
