# Expanding Grid：PC/Tablet向け UIデザインのブラッシュアップ

機能面は完璧に動作しました。続いて、ユーザーが階層構造を視覚的に理解しやすくなるよう、PC/Tablet向けのUIデザイン（CSS/SCSS）を以下の仕様にアップデートしてください。※JSのロジックは絶対に変更しないでください。

## 1. 開閉アイコン（矢印）の追加とアニメーション

すべての階層の `.eg-item__title`（ボタン）に対して、右端に開閉を示す「下矢印」アイコンを追加してください。

- `display: flex; justify-content: space-between; align-items: center;` を用いて、テキストとアイコンを両端揃えにすること。
- アイコンは `::after` 疑似要素を用いた CSSシェブロン（`border-right` と `border-bottom` を設定し `rotate(45deg)` する手法）、またはシンプルなインラインSVG背景を使用すること。
- `transition: transform 0.3s ease;` を設定し、`aria-expanded="true"` の状態の時はアイコンが180度回転（上矢印に変化）するようにすること。

## 2. 第1階層パネルの視覚的独立（白背景＋余白）

第1階層が開いたことを明確にするため、`[data-depth="0"] > .eg-panel` または内部の `.eg-item__content` に対して以下のスタイルを適用してください。

- `background-color: #ffffff;` を設定する。（SWELLの背景から浮かび上がらせるため）
- `border-radius: 4px;` と、ごく薄いボーダー（例: `1px solid #ddd`）を追加し、エリアを明確にする。
- パディングを `padding: 16px 24px 24px;` に変更し、左右に24pxの余白を設けて内部コンテンツが境界に張り付かないようにする。

## 3. 第2階層ボタンのデザイン差別化（下線スタイル）

第1階層と第2階層の区別をつけるため、`[data-depth="1"] > .eg-item > .eg-item__title` のデザインを「囲みボタン」から「下線付きタブ」へ変更してください。

- `background: transparent;` にし、周囲の `border` と `border-radius` を削除する。
- 代わりに `border-bottom: 2px solid #e0e0e0;` を設定し、フラットな見た目にする。
- `padding` は `12px 8px` 程度にスッキリさせること。

上記CSSを反映し、再度 `npm run build` を実行してください。

## #002

# Expanding Grid：UIの最終ブラッシュアップとクラス自動付与

素晴らしい進捗です！UIデザインに関して、アクセシビリティとモダンなルックを向上させるため、SCSSおよび出力ロジックに以下の変更を加えてください。

## 1. 第1階層（親）アクティブ時のアクセシビリティ向上

現在のアクティブ状態（グレー背景）は視認性が低いため、SWELLのメインカラーを利用して明確にハイライトします。

- `[data-depth="0"] > .eg-item > .eg-item__title[aria-expanded="true"]` に対して、`background-color: var(--color_main);` と `color: #fff;` を適用してください。
- アイコン（矢印）の色も白になるよう調整してください。

## 2. 第1階層コンテンツ背景のボーダー削除（モダン化）

第1階層の白いコンテンツエリアを囲むボーダーは、少し古いプラットフォームのUIに見えてしまうため削除します。

- `[data-depth="0"] > .eg-panel`（またはそれに相当する白い背景の要素）から `border` の指定を完全に削除してください。背景色 `#ffffff` とパディングのみで構成し、境界線をなくします。

## 3. 第2階層（子）アクティブ時のメインカラー化

現在どのプランを見ているかを直感的に伝えるため、第2階層の下線タブのアクティブ状態を強調します。

- `[data-depth="1"] > .eg-item > .eg-item__title[aria-expanded="true"]` に対して、`border-bottom-color: var(--color_main) !important;` を適用してください。
- テキストカラーと矢印アイコンも `color: var(--color_main);` に変更し、アクティブ状態を視覚的に際立たせてください。

## 4. `dp-wrap` クラスの自動付与（運用事故の防止）

SWELLデザインパターンのルールに則り、最外郭のコンテナに必ず `dp-wrap` クラスが付与されるようにします。ただし、店舗スタッフが手動でクラスを入力する手間や事故を防ぐため、プログラム側で強制付与します。

- PHPの動的レンダリング（`render_callback`）において、**`data-depth="0"` となる最上位の `.eg-wrapper` のクラスリストにのみ、自動的に `dp-wrap` を追加出力**するように修正してください。第1階層以降（子のラッパー）には付与しないでください。

上記を実装し、ビルド（`npm run build`）を実行してください。

## #003

# Expanding Grid：テーマ独立化とカスタムアイコン機能の実装

本プラグインをSWELL以外のテーマでも汎用的に動作させ、かつデザインの自由度を高めるため、以下の機能を実装してください。

## 1. ブロック属性（Attributes）の追加

- **親ブロック (`expanding-grid-wrapper`)**:
  - `mainColor`: ユーザーが選択するメインカラー（デフォルト: #0073aa ※WP標準青）
- **子ブロック (`expanding-grid-item`)**:
  - `prefixImageId`: メディアライブラリから選択された画像のID
  - `prefixImageUrl`: 選択された画像のURL

## 2. エディタ側（InspectorControls）の拡張

- **親ブロック**:
  - 設定パネルに「カラー設定」を追加。`ColorPalette` または `ColorPicker` を用いて `mainColor` を選択可能にする。
- **子ブロック**:
  - 設定パネルに「先頭アイコン画像」を追加。`MediaUpload` を使用し、メディアライブラリから画像（イラスト・アイコン等）を選択・解除できるようにする。

## 3. CSS/SCSS のテーマ独立化（インライン変数）

SWELLの `--color_main` に依存するのをやめ、親ブロックが持つ `mainColor` を利用します。

- PHPの `render_callback` において、最上位の `.eg-wrapper` の `style` 属性に `--eg-main-color: ${mainColor};` をインラインで出力してください。
- SCSS内では `var(--color_main)` をすべて `var(--eg-main-color)` に書き換えてください。

## 4. UIデザインの最終調整（指示に基づく修正）

- **第1階層（Active時）**: 背景色を `var(--eg-main-color)`、文字色を `#ffffff` に固定。
- **第1階層パネル**: 背景を白（#fff）にし、左右パディングを `24px` に。**周囲のボーダーは削除**。
- **第2階層（Active時）**: 下線（border-bottom）と文字色を `var(--eg-main-color)` に。
- **自動付与クラス**: 最上位ラッパーに `dp-wrap` クラスを自動付与するロジックを維持。

## 5. 先頭アイコン画像（Prefix Image）のレンダリング

- ボタン（`.eg-item__title`）内のテキストの直前に、画像を表示する `<img>` タグを挿入（設定されている場合のみ）。
- **画像サイズ**: 5:4（若干縦長）の比率で表示されるようCSSで制御。
- **余白制御**: 画像がある場合はテキストとの間に適切な余白を設け、画像がない場合はテキストが左端に詰まるように（`display: flex` と `gap` を活用）実装すること。

## 上記を実装し、`npm run build` を実行してください。

## #004

# バグ修正：レイアウト崩れの復旧とスタイルのマージ

実装ありがとうございます。しかし、フロントエンドのレイアウトが完全に崩壊してしまいました。原因は以下の2点です。確実に修正してください。

1. **インラインスタイルの上書きバグ（致命的）**
   `render.php` で `--eg-main-color` をインラインスタイルに出力する際、元々 `.eg-wrapper` に付与されていた `display:grid; grid-template-columns:repeat(4, 1fr); gap:16px;` 等の必須レイアウトスタイルを上書きして消去してしまっています。
   **既存のグリッドレイアウトの `style` 属性を維持したまま、そこに `--eg-main-color` を追加（マージ）するように `render.php` を修正してください。**

2. **ボタン内のFlexboxレイアウト崩れ**
   アイテムのボタン内に `<img class="eg-item__prefix-img">` と `<span class="eg-item__title-text">` を分離したことで、テキストの縦ズレなどの崩れが起きています。
   `.eg-item__title` に対して `display: flex; align-items: center; gap: 8px;` を適用し、画像とテキストが美しく横並びの中央揃えになるよう SCSS を調整してください。

修正後、`npm run build` を実行してください。

---

## #005

# 致命的なバグの完全修復：InnerBlocks消失とInspectorControlsの欠落

実装により、エディタおよびフロントエンドで致命的な崩壊が起きています。原因は `save.js` の誤った無効化と、サイドバーコンポーネント（InspectorControls）の欠落です。
以下の4点を【確実に】修正してください。

## 1. `save.js` の修正（中身の消失バグ）

`save.js` を `return null;` にしたことで、親ブロックに内包されるはずの `InnerBlocks` のデータが保存時にすべて消失し、フロントエンドが空っぽになっています。
親ブロックおよび子ブロックの `save.js` は `return null;` ではなく、**必ず `<InnerBlocks.Content />` を return するように修正**してください。（子ブロックで InnerBlocks を使っていない場合は、適切な静的HTMLまたは null を返して構いませんが、親は絶対に InnerBlocks.Content を返す必要があります）。

## 2. エディタUIの修正（巨大なカラー設定の移動）

親ブロックと子ブロックの `edit.js` において、「カラー設定」や「画像選択」のUIがブロック本体に直接レンダリングされており、エディタの表示を崩壊させています。
各種設定UI（`ColorPalette` や `MediaUpload` など）は、**必ず `<InspectorControls>` と `<PanelBody>` で囲み、エディタの「右サイドバー」に表示されるように移動**させてください。ブロック本体（`useBlockProps` の中）には `InnerBlocks`（とプレフィックス画像等）のみをレンダリングしてください。

## 3. インラインスタイルの上書きバグ（レイアウト崩壊）

`render.php` で `--eg-main-color` をインラインスタイルに出力する際、元々 `.eg-wrapper` に付与されていた `display:grid; grid-template-columns:repeat(4, 1fr); gap:16px;` 等の必須レイアウトスタイルを上書き消去してしまっています。
既存のグリッドレイアウトのスタイルを維持したまま、そこに `--eg-main-color: ${mainColor};` を追加（マージ）するように `render.php` を修正し、さらに内部に **`$content`（InnerBlocksの中身）を必ず echo** してください。

## 4. アイコン画像（Prefix Image）のレイアウト修正

子ブロックの `.eg-item__title` 内で画像とテキストが縦ズレしないよう、SCSSに以下を適用してください。

```scss
.eg-item__title {
  display: flex;
  align-items: center;
  gap: 12px;
}
.eg-item__prefix-img {
  width: auto;
  height: 1.5em; /* フォントサイズに合わせた高さを基準にする */
  aspect-ratio: 4/5;
  object-fit: cover;
  border-radius: 2px;
}
```

上記4点を修正し、npm run build を実行してください。

---

## #006

# 1. 致命的なバグの修正：二重ラッパーの解消とエディタUIのクリーンアップ

カラー設定とアイコン実装の方向性は良いですが、DOM構造の重複とUIの配置ミスによりレイアウトが崩壊しています。以下の2点を確実に修正してください。

- **二重ラッパーの解消**: `render.php` で `$content` を新たな `<div class="eg-wrapper">` で囲んで出力するのをやめてください。既存の `$content` のルートタグに対して、`WP_HTML_Tag_Processor` を用いて `dp-wrap` クラスや `--eg-main-color` のスタイルを直接注入・マージして出力し、DOMの階層（マトリョーシカ状態）を絶対に増やさないでください。
- **エディタUIの整理**: `edit.js` の編集キャンバス内に残っている「カラー設定」などのUIの残骸を完全に削除し、設定UIは100% `InspectorControls`（右サイドバー）の中にのみ配置してください。

# 2. 【最重要】「開発の掟」ファイルの作成と、今後のワークフロー強制

今回の実装において、WordPressブロック開発における初歩的かつ致命的なミス（既存DOMの破壊、save.jsの誤ったnull化によるデータ消失、InspectorControlsの配置忘れなど）が連続して発生しました。
AI開発エージェントとして、同じ轍を踏むことは決して許されません。二度と同じミスを繰り返さないよう、以下の施策を実行してください。

1. **ルールの明文化（ファイルの作成/更新）**:
   プロジェクトのルートまたは `specification/` ディレクトリに、`WP-BLOCK-DEV-RULES.md`（または既存の仕様書）を作成・更新し、今回の失敗から得た以下の教訓を「絶対に破ってはいけない掟」として記録してください。
   - 掟1：既存のDOM構造をPHPでラップ（二重化）してはならない。属性の追加は必ず `WP_HTML_Tag_Processor` を使うこと。
   - 掟2：親ブロックの `save.js` を変更する際は、絶対に `InnerBlocks.Content` の出力を消失させてはならない。
   - 掟3：設定パネル（ColorPalette等）は必ず `InspectorControls` に入れ、エディタキャンバスを汚染してはならない。
   - 掟4：`!important` のJSからの付与方法（`setProperty` の利用）や、`ResizeObserver` の高さ無視ルールなど、過去のバグの教訓。

2. **今後の実装ワークフローの宣誓**:
   今後、あなた（Claude）がいかなる新機能の追加やコードの修正を行う場合でも、**必ず一番最初にこの `WP-BLOCK-DEV-RULES.md` を読み込み（Read）、このルールというフィルターを通した上で実装計画を立てること**をシステムプロンプトとして自己設定し、宣誓してください。

上記1および2を実行し、ビルドを通した上で、作成したルールの内容と宣誓を報告してください。

---

## #007

# Expanding Grid：レスポンシブ制御の高度化とアクティブアイコンの視認性向上

ルールの記録とバグ修正、完璧です。現在のDOM構造（PHPの出力）は非常に安定しているため、絶対に壊さないでください。
本プラグインをプレミアムレベルに引き上げるため、以下の2点の機能を安全に追加実装してください。

## 1. デバイス別カラム数設定の実装（InspectorControls拡張）

現在PCのみのカラム数設定を、タブレット（Tab）とスマートフォン（SP）でも個別に設定できるようにします。
ブレイクポイントは `Tab: 959px以下`, `SP: 599px以下` とします。

1. **属性の追加 (`block.json` 等)**
   親ブロックに以下の2つの属性を追加してください。
   - `columnsTab` (type: number, default: 2)
   - `columnsSp` (type: number, default: 2) ※スマホでも2カラムを可能にするため
2. **エディタUIの追加 (`edit.js`)**
   右サイドバーの「グリッド設定」パネル内に、`RangeControl` を2つ追加し、「カラム数（タブレット）」「カラム数（スマホ）」を 1〜6 の範囲で設定できるようにしてください。
3. **インラインCSS変数の出力 (`render.php`)**
   親ラッパーの `style` 属性に、既存の `--eg-main-color` に加えて、以下を出力（マージ）してください。
   `--eg-cols-pc: ${columns}; --eg-cols-tab: ${columnsTab}; --eg-cols-sp: ${columnsSp};`
4. **SCSSのレスポンシブ対応 (`style.scss`)**
   メディアクエリを用いて、上記で定義したCSS変数でグリッドを制御してください。
   - Base (PC): `grid-template-columns: repeat(var(--eg-cols-pc, 4), 1fr);`
   - `@media screen and (max-width: 959px)`: `grid-template-columns: repeat(var(--eg-cols-tab, 2), 1fr) !important;`
   - `@media screen and (max-width: 599px)`: `grid-template-columns: repeat(var(--eg-cols-sp, 2), 1fr) !important;`

## 2. 第1階層アクティブ時のアイコン視認性確保

第1階層のボタンがアクティブになった際、背景色が `mainColor` になるため、ユーザーが設定した黒系のアイコン（`prefixImage`）が見えなくなる問題を解決します。

- SCSSにて、第1階層のアクティブボタン内にある画像に対してのみ、背景を白にする以下のスタイルを追加してください。

```scss
.eg-wrapper[data-depth="0"]
  > .eg-item
  > .eg-item__title[aria-expanded="true"]
  .eg-item__prefix-img {
  background-color: #ffffff;
  padding: 2px; /* 白背景の余白を少し持たせる */
  border-radius: 4px; /* 角丸にして綺麗に見せる */
  /* ※画像のサイズ(height: 1.5em等)は既存のものを維持してください */
}
```

【注意事項】
WP-BLOCK-DEV-RULES.md の掟に則り、render.php での WP_HTML_Tag_Processor を使った注入ロジックや、DOM階層は絶対に変更しないでください。追加するインラインスタイル（CSS変数）を文字列として安全に結合するだけに留めてください。

実装後、npm run build を実行してください。

---

## #008

# 最終デバッグ：第2階層（ネスト）へのレスポンシブカラム設定の適用

デバイス別のカラム数設定は第1階層には完璧に動作していますが、第2階層（`data-depth="1"` 以降）には適用されず、1カラムのままになっています。
原因は、過去の実装でネストされたラッパーに対して `flex` による縦積みを強制しているレガシーCSSが残存しており、グリッド設定を上書き（ブロック）しているためです。

**※注意：これまでのバグの教訓（掟）に従い、PHP（render.php）やJSは1ミリも変更しないでください。SCSS（`style.scss` および `editor.scss`）のみを修正します。**

以下の3点を修正してください。

1. **ネストされたラッパーの `flex` 強制を解除（フロントエンド）**
   フロントエンドの `style.scss` 内にある `.eg-wrapper[data-depth="1"]` や `.eg-wrapper[data-depth="2"]` に対する `display: flex !important;`, `flex-direction: column;`, `grid-template-columns: unset !important;` の指定を**すべて削除**し、親ブロックと同じように `display: grid;` が効くようにしてください。

2. **全階層へのグリッド変数の適用（フロントエンド）**
   フロントエンドにおいて、階層（`data-depth`）に関わらず、すべての `.eg-wrapper` がCSS変数（`--eg-cols-pc`, `--eg-cols-tab`, `--eg-cols-sp`）に従って正しくグリッドレイアウトになるよう、メディアクエリの指定が `[data-depth="0"]` 限定になっていれば、汎用的な `.eg-wrapper` へ適用範囲を広げてください。

3. **エディタ専用スタイルの隔離（エディタUXの担保）**
   「エディタ画面ではネストが深くなると編集しづらいので、縦積み（1カラム）にする」というエディタ側のUX要件は維持する必要があります。
   フロント側のCSSからは上記の通り削除しますが、その代わりに `editor.scss` 内、または `.editor-styles-wrapper .eg-wrapper[data-depth="1"]` のようにエディタ内限定のスコープでのみ `display: flex; flex-direction: column;` が適用されるように整理してください。

上記SCSSの調整のみを行い、再度 `npm run build` を実行してください。

---

## #009

# 最終工程：本番配布用プラグインのZIPパッケージ化

開発とデバッグが完了しました。他のWordPress環境（別テーマ等）で動作テストを行うため、本番配布用のZIPファイルを生成します。

1. `package.json` の `scripts` に以下のコマンドを追加してください（すでに存在する場合はスキップ）。
   `"plugin-zip": "wp-scripts plugin-zip"`

2. `npm run build` を実行して、最新のビルドが適用されていることを確認してください。

3. `npm run plugin-zip` を実行して、プラグインのZIPファイルを作成してください。

実行後、生成されたZIPファイルのファイル名と保存場所（パス）を報告してください。
