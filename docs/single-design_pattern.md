## 【Claude Code向け 実装仕様書】DP詳細ページ ヘルプUI構築

**■ 目的**
SWELLデザインパターンの詳細ページにおいて、ユーザーのコピペ後のカスタマイズを支援する「専用ガイド（初期表示）」と「共通ガイド（アコーディオン）」のUIを構築する。データ入力者の負担を減らすため、専用ガイドはACFの「プルダウン選択」をベースに自動出力する仕組みとする。

### 1. ACF（Advanced Custom Fields）の設計

対象：カスタム投稿タイプ `design_pattern`

「専用ガイド」を入力するための繰り返し（Repeater）フィールドを作成する。自由記述を極力減らし、プルダウンで選択するだけで標準的なTIPS（動画や図解）が出力されるようにする。

- **フィールドグループ名:** カスタマイズTIPS設定
- **フィールド1: TIPSリスト (Repeater: `dp_custom_tips`)**
  - **サブフィールド1-A: TIPSの種類 (Select: `tip_type`)**
    - 選択肢（例）:
      - `change_column : カラム数（列数）の変え方`
      - `change_image : 画像の形・サイズの変え方`
      - `change_color : 背景色・ボタンの色の変え方`
      - `add_item : リストやステップの追加方法`
      - `custom : 【自由入力】独自のTIPS`
    - 必須項目とする。
  - **サブフィールド1-B: 独自TIPSタイトル (Text: `custom_tip_title`)**
    - 条件判定: `tip_type` が `custom` の場合のみ表示
  - **サブフィールド1-C: 独自TIPS内容 (Wysiwyg or Textarea: `custom_tip_content`)**
    - 条件判定: `tip_type` が `custom` の場合のみ表示

### 2. 定型TIPSデータの中央管理（PHP側）

プルダウンで選択された定型TIPSの「出力内容（タイトル、動画URL/画像URL、説明文）」は、テンプレート側（または `functions.php` / 専用のインクルードファイル）に配列として定義する。
これにより、将来「動画を差し替えたい」となった場合も、全パターンの記事を編集することなく一括で更新可能になる。

**【実装イメージ（PHP配列）】**

```php
$standard_tips = [
    'change_column' => [
        'title' => 'カラム数（列数）の変え方',
        'media' => '<video src=".../column.mp4" autoplay loop muted playsinline></video>',
        'text'  => '外側の「カラムブロック」を選択し、右側パネルの設定からカラム数を変更してください。',
    ],
    'change_color' => [
        'title' => '背景色・ボタンの色の変え方',
        // ...以下略
    ],
];
```

### 3. フロントエンド（詳細ページ）の出力構造

対象ファイル: `single-design_pattern.php` （または該当するテンプレート）

「ブロックコードをコピーする」ボタンの直下に、以下のHTML構造で出力すること。

**【出力ロジック】**

1.  **専用ガイド（初期表示）:**
    - ACFの `dp_custom_tips` をループ処理。
    - 最大3件まで表示。
    - `tip_type` が `custom` 以外なら `$standard_tips` 配列からデータを引いて出力。
    - `custom` なら ACFの入力値を出力。
2.  **共通ガイド（初期非表示 / SWELLアコーディオン）:**
    - SWELLの標準アコーディオン用HTMLクラス（`.swell-block-accordion` 等）を手動で記述し、その内部コンテンツとしてブログパーツのショートコードを展開する。

**【DOM構造（HTML）要件】**

```html
<div class="dp-help-area">
  <div class="dp-help-specific">
    <h3 class="dp-help-title">このパターンのカスタマイズ方法</h3>
    <ul class="dp-help-list">
      <li class="dp-help-item">
        <div class="dp-help-item__media">動画または画像</div>
        <div class="dp-help-item__content">
          <h4>タイトル</h4>
          <p>説明テキスト</p>
        </div>
      </li>
    </ul>
  </div>

  <div class="dp-help-common swell-block-accordion">
    <div class="swell-block-accordion__head">
      <span>＋ その他の使い方・トラブルシューティングを見る</span>
    </div>
    <div class="swell-block-accordion__body">
      <?php echo do_shortcode('[swell_bp id="〇〇"]'); //
      共通ガイド用ブログパーツID ?>
    </div>
  </div>
</div>
```

### 4. スタイリング（SCSS）の制約

- クラス名は `dp-help-` プレフィックスで統一すること。
- アコーディオンの開閉アニメーションはSWELLの標準JSに依存するため、HTMLのクラス構造（`.swell-block-accordion`, `__head`, `__body`）はSWELLの仕様（ブロックエディタで出力される生のHTML）に完全に一致させること。
- 動画（mp4）は軽量化のため `max-width: 100%;` とし、スマホ閲覧時に邪魔にならないサイズ感（高さ制限など）を設けること。
