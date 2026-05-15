# カスタムブロックプラグイン開発：clickable-wrapper-block

> 作成日：2026-05-14
> ステータス：設計中
> 説明：内包する要素全体を`<a>`タグで囲む汎用リンクラッパーブロック

---

## プロジェクト概要

Elementorのように、Gutenbergブロックエディタでカード全体など任意のブロック群をリンク化できる汎用ラッパーブロック。
SWELLを含む任意のWPテーマで動作する。ノーコードユーザーが直感的にリンクを設定できるUIを提供する。

---

## 設計方針

| 決定項目         | 選択                           | 理由                                           |
| ---------------- | ------------------------------ | ---------------------------------------------- |
| レンダリング方式 | PHP render_callback（動的）    | 将来の属性追加時にバリデーションエラーを防ぐ   |
| save.js          | `<InnerBlocks.Content />` のみ | 外枠なし＝動的レンダリングとの組み合わせで安全 |
| edit.js 外枠     | `<div>` ＋ 破線スタイル        | エディタ内クリック誤遷移を防止                 |
| テーマ依存       | なし                           | 汎用プラグインとして配布可能にする             |

---

## ファイル構成

```
wp-content/plugins/clickable-wrapper-block/
├── clickable-wrapper-block.php   # ブロック登録・render_callback
├── package.json
├── WP-BLOCK-DEV-RULES.md
├── build/
└── src/
    └── clickable-wrapper-block/
        ├── block.json
        ├── edit.js               # エディタ表示（<div>ラッパー＋破線）
        ├── save.js               # <InnerBlocks.Content /> のみ
        ├── render.php            # フロント出力（<a>タグ動的生成）
        ├── style.scss            # フロント用CSS（最小限）
        └── editor.scss           # エディタ用CSS（破線スタイル等）
```

---

## ブロック仕様

### ブロック名

`clickable-wrapper-block/wrapper`

### attributes

```json
{
  "linkUrl": { "type": "string", "default": "" },
  "linkTarget": { "type": "boolean", "default": false },
  "rel": { "type": "string", "default": "noopener noreferrer" }
}
```

### エディタUI（InspectorControls）

- URL入力欄（TextControl）
- 「新しいタブで開く」トグル（ToggleControl）
- URLが空のとき: 破線ボーダー＋「URLを設定してください」の注意表示

### フロント出力（render.php）

```php
$url    = esc_url( $attributes['linkUrl'] ?? '' );
$target = ! empty( $attributes['linkTarget'] ) ? '_blank' : '';
$rel    = esc_attr( $attributes['rel'] ?? 'noopener noreferrer' );

// $url が空のときは <div> にフォールバック（リンクなしで表示）
if ( $url ) {
    printf( '<a href="%s" target="%s" rel="%s" class="cwb-wrapper">%s</a>', $url, $target, $rel, $content );
} else {
    printf( '<div class="cwb-wrapper cwb-wrapper--no-link">%s</div>', $content );
}
```

---

## 実装・修正時のチェックリスト

開発掟（WP-BLOCK-DEV-RULES.md）を必ず参照すること。

| #   | 確認項目                   | 合格条件                                            |
| --- | -------------------------- | --------------------------------------------------- |
| 1   | save.js の返り値           | `<InnerBlocks.Content />` のみ（外枠なし）          |
| 2   | 設定UI配置                 | InspectorControls 内に収まっているか                |
| 3   | URLなし時のフォールバック  | `<div>` で表示崩れしないか                          |
| 4   | `target="_blank"` 時の rel | `noopener noreferrer` が付与されているか            |
| 5   | エディタ内クリック         | 誤遷移しないか（edit.js が `<div>` になっているか） |

---

## 作業ログ

- 2026-05-14 初版作成・雛形生成完了

# プラグイン実装（Gutenberg カスタムブロック）

- プラグイン名: Clickable Wrapper Block (仮)
- 目的: Elementorのように、内包する要素全体を`<a>`タグで囲むことができるGutenberg用ラッパーブロックを開発する。
- ターゲット: SWELL等の環境で、カード全体を直感的にリンク化したいノーコードユーザー。
- 実装詳細:
  1. ブロック構成: `InnerBlocks` を使用し、直下に任意のブロック（画像、見出し、段落など）を自由に配置できるようにする。
  2. 属性(Attributes): `linkUrl` (文字列), `linkTarget` (ブール値: 新タブで開くか), `rel` (文字列) を持たせる。
  3. エディタ画面 (edit.js):
     - 右側のInspectorControls（設定パネル）にURL入力欄、リンクターゲット設定を配置。
     - ※重要: エディタ上でのクリックによる画面遷移の誤動作を防ぐため、エディタ側（edit.js）では外枠を`<div>`タグで出力し、視覚的に「リンク範囲」であることがわかる破線などのスタイルを当てること。
  4. フロント表示 (save.js):
     - 最外郭のラッパーを `<a>` タグとして出力し、その中に `<InnerBlocks.Content />` を展開する。
     - href属性に `linkUrl` を出力し、`linkTarget` が true の場合は `target="_blank"` と `rel="noopener noreferrer"` を付与。
     - Gutenbergコメント（`<!-- wp: ... -->`）を絶対に破壊・改変させない標準的な作法を厳守すること。
- 開発ステップ:
  1. プラグインのベースとなるPHPファイルの作成（ブロック登録処理）。
  2. `block.json`, `index.js` (edit/save) の作成。
  3. `@wordpress/scripts` を用いたビルド環境のセットアップとビルド実行。

---

# 同意内容

素晴らしい視点での提案、ありがとうございます！
クライアントが将来エディターを開いた際に「ブロックのリカバリーを試行」というエラー画面を見る事態は絶対に避けたいです。運用UXと将来の保守性を最優先するため、ご提案いただいた【render_callback（動的レンダリング）方式】で進めてください。

仕様は以下で確定とします：

1. フロント出力 (PHP): `render_callback` を使用して、属性（linkUrl等）を元に `<a>` タグを動的に生成し、その中に `$content` (InnerBlocksの中身) を展開する。
2. 保存処理 (save.js): 外枠は持たせず、`<InnerBlocks.Content />` のみを保存する仕様とする。
3. エディタ処理 (edit.js): 前回の仕様通り、誤遷移を防ぐため `<div>` でラッパーを作り、クリック範囲と分かる視覚的スタイル（破線など）を当てる。

この方針で、雛形の生成および実装をスタートしてください！爆速で終わらせましょう！

---

# 雛形の同意と実行依頼

完璧なセットアップありがとうございます！ドキュメント類もバッチリですね。
それでは、確定した【render_callback（動的レンダリング）方式】で実装を進めてください。GOです！

実装にあたり、運用UX（ノーコードユーザーの使いやすさ）を最大化するため、以下の詳細要件を各ファイルに反映してください。

1. **block.json & PHP登録:**
   - 属性として `linkUrl` (string), `linkTarget` (boolean, default: false), `rel` (string, default: 'noopener noreferrer') を定義。
   - 動的ブロックとして `render.php`（またはPHPファイルでのコールバック）を正しく紐付け。

2. **edit.js (エディタ画面のUX最優先):**
   - **右パネル (InspectorControls):**
     - URLを入力するテキストフィールド（URLInputなど）。
     - 「新しいタブで開く」のトグルスイッチ（ToggleControl）。
   - **エディタ上の表示:**
     - 誤遷移を防ぐためラッパーは `<div>` を使用。
     - ユーザーが「ここはリンクの範囲だ」と直感的に分かるように、エディタ上限定のCSS（例: `border: 2px dashed #007cba; padding: 1rem; border-radius: 4px;` など）を適用。
     - ラッパー内に `<InnerBlocks />` を配置し、自由にブロックを追加できるようにする。

3. **save.js:**
   - 余計なHTMLを出力せず、シンプルに `return <InnerBlocks.Content />;` のみとする。

4. **render.php (フロント出力):**
   - `$attributes['linkUrl']` が存在する場合は `<a>` タグで `$content` を囲む。
   - `linkTarget` が true の場合は `target="_blank"` と `rel="noopener noreferrer"` を付与。
   - URLが未設定の場合は、リンク化せずにそのまま `$content` を出力する（安全設計）。

この仕様でコードを書き換え、ビルド（`npm run build`）まで一気に完了させてください！期待しています！

---

# 追加要件01 Clickable Wrapper Block: デザインサポート機能の追加

- 目的: ブロックに対して、背景色、枠線、パディングをエディターの標準UIで設定できるようにする。
- 実装方針: 独自のInspectorControlsは作らず、WordPressコアの `supports` API を最大限に活用し、SWELL等のテーマ設定を継承させる。

- 実装詳細:
  1. **block.json:**
     - `supports` プロパティに以下を追加し、標準UIを有効化する。
       - `color`: `background`, `text`, `link`
       - `spacing`: `padding`, `margin`
       - `border`: `color`, `radius`, `style`, `width`
  2. **edit.js (エディター画面):**
     - `useBlockProps()` を正しく適用し、エディターの右パネルで設定した色や余白がリアルタイムでラッパー `<div>` に反映されるようにする。
     - **【重要：エディターUXの絶対防衛線】** 色や余白は反映させるが、フロントエンドのレイアウト制約（固定高や `overflow: hidden`、絶対配置など）はエディターに持ち込まないこと。InnerBlocksの要素が常に見切れず、すべてクリック・編集できるフレキシブルな状態を絶対維持すること。
     - 背景や枠線が「未設定」の時でも操作しやすいよう、必要に応じて薄い破線などのガイドスタイルをエディター専用として残すこと。
  3. **render.php (フロント出力):**
     - `get_block_wrapper_attributes()` を活用して、エディターで設定されたスタイル（クラス名やインラインCSS）を、最外郭の `<a>` タグに動的にマージして出力する。
     - 既存の `href`, `target`, `rel` 属性の出力ロジックと競合しないよう注意する。

この仕様で `block.json`, `edit.js`, `render.php` を更新し、ビルドを実行してください。
