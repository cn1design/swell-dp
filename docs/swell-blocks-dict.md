# SWELL Block Dictionary

> **Cursorへの指示:** 新しいデザインパターンを生成する際は、このファイルのベースHTMLを必ず起点にすること。ゼロからブロックコードを推測・生成することは禁止。

---

## DUMMY ASSETS（固定定数）

> **Cursorへの指示:** ブロックコードに画像が含まれる場合、srcとクラスは必ず以下の固定値を使用すること。URLを推測・変更しないこと。

| 項目             | 固定値                                                                  |
| ---------------- | ----------------------------------------------------------------------- |
| ダミー画像URL    | `http://localhost:10054/wp-content/uploads/2026/03/no-image_dammy.webp` |
| ダミー画像クラス | `wp-image-dummy`                                                        |

**運用ルール:**

- `wp-image-dummy` は本番画像に置き換えた時点でWPが自動的に正しいIDに更新する
- ローカル確認時はダミー画像がそのまま表示される
- 本番環境へ持ち出す前に必ずメディアライブラリで画像を置換すること

```html
<!-- 画像ブロックの記述例（この形式で統一） -->
<img
  src="http://localhost:10054/wp-content/uploads/2026/03/no-image_dammy.webp"
  alt="画像の説明"
  class="wp-image-dummy"
/>
```

---

## 使い方

1. デザインカンプ（スクショ）と合わせて、対応するTYPEのベースHTMLを指定する
2. 「TYPE-Bをベースに、このデザインになるようにテキストを差し替え・クラスを付与してSCSSを出力して」と指示する
3. ブロックコードは提供されたHTMLのGutenbergコメントを絶対に改変しないこと

---

## TYPE-A: フルワイドブロック（セクション外枠の基本型）

**使用場面:** 全パターンの最外殻。背景色・背景画像・上下余白を持つセクション全体のラッパー。
**カスタマイズ可能箇所（GUI）:** 背景色、背景画像、上下パディング（pc-py-〇〇 / sp-py-〇〇クラスで制御）
**dp-クラスの付与位置:** 最外郭の `swell-block-fullWide` に `dp-〇〇-section` と `dp-wrap` を必ず両方付与する

**⚠️ 必須ルール:**

- 最外郭は必ず `wp:loos/full-wide` を使うこと（`wp:group {"align":"full"}` は使わない）
- `contentSize` は必ず `"container"` にすること（`"full"` は使わない）
- `dp-wrap` クラスは全パターン共通で必ず付与すること（h2リセット・モーダル無効化のスコープ）
- インナーdivには `l-container` クラスを付与すること

```html
<!-- wp:loos/full-wide {"bgOpacity":0,"contentSize":"container","className":"dp-〇〇-section dp-wrap"} -->
<div
  class="swell-block-fullWide pc-py-60 sp-py-40 alignfull dp-〇〇-section dp-wrap"
>
  <div class="swell-block-fullWide__inner l-container">
    <!-- ★ ここにセクションタイトル・本体コンテンツを入れる -->
  </div>
</div>
<!-- /wp:loos/full-wide -->
```

---

## TYPE-B: セクションタイトル（英語サブ＋日本語見出しH2）

**使用場面:** 各セクションの冒頭。英語のサブテキスト（小）＋ 日本語H2（大）の2行構成。
**カスタマイズ可能箇所（GUI）:** テキスト内容、文字色、文字サイズ（エディタで調整）
**dp-クラスの付与位置:** 外枠グループに `dp-section-title` を付与

```html
<!-- wp:group {"className":"dp-section-title","layout":{"type":"constrained"}} -->
<div class="wp-block-group dp-section-title">
  <!-- wp:paragraph {"className":"dp-section-title__en"} -->
  <p class="dp-section-title__en">ENGLISH SUB TITLE</p>
  <!-- /wp:paragraph -->

  <!-- wp:heading {"level":2,"className":"dp-section-title__ja"} -->
  <h2 class="wp-block-heading dp-section-title__ja">日本語の大見出し</h2>
  <!-- /wp:heading -->
</div>
<!-- /wp:group -->
```

---

## TYPE-C: 3カラム均等分割（カード型の基本）

**使用場面:** 選ばれる理由・特徴・サービス紹介など、横並びの繰り返しカード。
**カスタマイズ可能箇所（GUI）:** カラム数（2〜4に変更可）、各カード内のテキスト・画像
**dp-クラスの付与位置:** `wp-block-columns` に `dp-〇〇-grid` を付与

> ⚠️ カラム数を変更する場合、`wp:columns` と各 `wp:column` の `columnCount` / `width` の数値も合わせて変更すること。

```html
<!-- wp:columns {"className":"dp-〇〇-grid"} -->
<div class="wp-block-columns dp-〇〇-grid">
  <!-- wp:column {"width":"33.33%"} -->
  <div class="wp-block-column" style="flex-basis:33.33%">
    <!-- wp:group {"className":"dp-〇〇-card","layout":{"type":"constrained"}} -->
    <div class="wp-block-group dp-〇〇-card">
      <!-- wp:image {"className":"dp-〇〇-card__img"} -->
      <figure class="wp-block-image dp-〇〇-card__img">
        <img
          src="http://localhost:10054/wp-content/uploads/2026/03/no-image_dammy.webp"
          alt="画像の説明"
          class="wp-image-dummy"
        />
      </figure>
      <!-- /wp:image -->

      <!-- wp:heading {"level":3,"className":"dp-〇〇-card__title"} -->
      <h3 class="wp-block-heading dp-〇〇-card__title">カード見出し</h3>
      <!-- /wp:heading -->

      <!-- wp:paragraph {"className":"dp-〇〇-card__text"} -->
      <p class="dp-〇〇-card__text">説明テキストが入ります。</p>
      <!-- /wp:paragraph -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:column -->

  <!-- ★ 同じ構造のカラムをカード数分繰り返す -->
</div>
<!-- /wp:columns -->
```

---

## TYPE-D: メディアとテキスト（2カラム 画像＋テキスト）

**使用場面:** 選ばれる理由・実績紹介など、画像とテキストを左右に並べるジグザグレイアウト。
**カスタマイズ可能箇所（GUI）:** 画像の左右反転（エディタの「コンテンツを反転」ボタン）、画像URL、テキスト内容
**dp-クラスの付与位置:** `wp-block-media-text` に `dp-〇〇-block` を付与

> ⚠️ 左右反転は `"mediaPosition":"right"` を変更することで制御される。エディタGUIで操作するのが最も安全。

```html
<!-- wp:media-text {"mediaPosition":"left","mediaType":"image","className":"dp-〇〇-block"} -->
<div class="wp-block-media-text dp-〇〇-block is-stacked-on-mobile">
  <figure class="wp-block-media-text__media">
    <img
      src="http://localhost:10054/wp-content/uploads/2026/03/no-image_dammy.webp"
      alt="画像の説明"
      class="wp-image-dummy"
    />
  </figure>

  <div class="wp-block-media-text__content">
    <!-- wp:heading {"level":3} -->
    <h3 class="wp-block-heading">見出しテキスト</h3>
    <!-- /wp:heading -->

    <!-- wp:paragraph -->
    <p>本文テキストが入ります。</p>
    <!-- /wp:paragraph -->
  </div>
</div>
<!-- /wp:media-text -->
```

---

## TYPE-E: SWELLステップブロック（旧API）

> ⚠️ **非推奨。** `wp:swell/step` は旧APIのため、新規実装は **TYPE-M**（`wp:loos/step`）を使用すること。

**使用場面:** 流れ・手順・ステップを番号付きで表示するセクション。
**カスタマイズ可能箇所（GUI）:** ステップ数（アイテムの追加削除）、各ステップのタイトル・テキスト・アイコン画像
**dp-クラスの付与位置:** `swell-block-step` の親グループに `dp-〇〇-steps` を付与

```html
<!-- wp:group {"className":"dp-〇〇-steps","layout":{"type":"constrained"}} -->
<div class="wp-block-group dp-〇〇-steps">
  <!-- wp:swell/step -->
  <div class="swell-block-step">
    <!-- wp:swell/step-item -->
    <div class="swell-block-step__item">
      <div class="swell-block-step__num">
        <span>01</span>
      </div>
      <div class="swell-block-step__body">
        <!-- wp:heading {"level":3} -->
        <h3 class="wp-block-heading">ステップタイトル</h3>
        <!-- /wp:heading -->
        <!-- wp:paragraph -->
        <p>ステップの説明テキストが入ります。</p>
        <!-- /wp:paragraph -->
      </div>
    </div>
    <!-- /wp:swell/step-item -->

    <!-- ★ 同じ構造のstep-itemをステップ数分繰り返す。numは02, 03...と連番 -->
  </div>
  <!-- /wp:swell/step -->
</div>
<!-- /wp:group -->
```

---

## TYPE-F: FAQアコーディオン（SWELLアコーディオンブロック）

**使用場面:** よくある質問セクション。クリックで開閉するアコーディオン形式。
**カスタマイズ可能箇所（GUI）:** Q&Aの追加削除、質問・回答テキストの編集
**dp-クラスの付与位置:** 親グループに `dp-〇〇-faq` を付与

```html
<!-- wp:group {"className":"dp-〇〇-faq","layout":{"type":"constrained"}} -->
<div class="wp-block-group dp-〇〇-faq">
  <!-- wp:swell/accordion {"headingTag":"h3"} -->
  <div class="swell-block-accordion">
    <div class="swell-block-accordion__head">
      <h3>よくある質問のタイトルが入ります</h3>
    </div>
    <div class="swell-block-accordion__body">
      <!-- wp:paragraph -->
      <p>回答テキストが入ります。</p>
      <!-- /wp:paragraph -->
    </div>
  </div>
  <!-- /wp:swell/accordion -->

  <!-- ★ 同じ構造のaccordionをQ&A数分繰り返す -->
</div>
<!-- /wp:group -->
```

---

## TYPE-G: CTAセクション（テキスト＋ボタン）

**使用場面:** セクション末尾やページ下部のCall to Action。背景色付きが多い。
**カスタマイズ可能箇所（GUI）:** ボタンテキスト・リンクURL・背景色・テキスト内容
**dp-クラスの付与位置:** 外枠フルワイドグループに `dp-〇〇-cta` を付与（TYPE-Aと組み合わせて使用）

```html
<!-- wp:group {"className":"dp-〇〇-cta","layout":{"type":"constrained"}} -->
<div class="wp-block-group dp-〇〇-cta">
  <!-- wp:heading {"level":2,"textAlign":"center"} -->
  <h2 class="wp-block-heading has-text-align-center">CTAの見出し</h2>
  <!-- /wp:heading -->

  <!-- wp:paragraph {"align":"center"} -->
  <p class="has-text-align-center">サポートテキストが入ります。</p>
  <!-- /wp:paragraph -->

  <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
  <div class="wp-block-buttons">
    <!-- wp:button {"className":"dp-〇〇-cta__btn"} -->
    <div class="wp-block-button dp-〇〇-cta__btn">
      <a class="wp-block-button__link" href="リンクURL">ボタンテキスト</a>
    </div>
    <!-- /wp:button -->
  </div>
  <!-- /wp:buttons -->
</div>
<!-- /wp:group -->
```

---

## TYPE-H: 横並びフレックスレイアウト（左テキスト＋右コンテンツ）

**使用場面:** 左にH2＋リード文＋本文、右にグリッドや画像など。標準の「カラムブロック」を使わず、グループブロックのFlex機能を活用して実装するレイアウト（よくある悩みセクション等で使用）。
**カスタマイズ可能箇所（GUI）:** 左テキストの内容、右コンテンツの種類やアイテム数（SWELLリッチカラム等）。
**dp-クラスの付与位置:** このコンテナ自体にはあえて独自の `dp-` クラスを付与しない。

> ⚠️ **実装の絶対ルール:**
> WordPress標準のカラムブロックはインラインスタイルが強制されるため使用しない。
> 代わりに `wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}}` を親とする。
> スタイリング（左右の幅の比率やSP時の縦積み制御）は、最外郭のセクションクラス（例: `.dp-problem-section-band`）を起点とし、`> .swell-block-fullWide__inner > .wp-block-group:first-child` や `:nth-child` などを利用してSCSS側で制御すること。

```html
<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"></div>
<!-- /wp:group -->
```

---

## TYPE-I: ブリッジコピー中央配置

**使用場面:** セクション間の橋渡しコピー。「そのお困りごと、〇〇にお任せください」など、前後のセクションをつなぐ短文を中央に大きく表示する。
**カスタマイズ可能箇所（GUI）:** テキスト内容、文字サイズ・色（エディタで調整）
**dp-クラスの付与位置:** 外枠グループに `dp-bridge-copy` を付与

```html
<!-- wp:group {"className":"dp-bridge-copy","layout":{"type":"constrained"}} -->
<div class="wp-block-group dp-bridge-copy">
  <!-- wp:paragraph {"align":"center"} -->
  <p class="has-text-align-center">そのお困りごと、<br>サンプルテンプレにお任せください</p>
  <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
```

---

## TYPE-J: SWELLリッチカラム（2カラム 画像＋テキスト）

**使用場面:** 画像とテキストを左右に並べる2カラムレイアウト。`wp:media-text` の代わりに使用すること。
**カスタマイズ可能箇所（GUI）:** カラム数・各カラムの内容
**dp-クラスの付与位置:** `wp:loos/columns` に `dp-〇〇-columns` を付与

> ⚠️ 特に指定がない場合の画像＋テキスト2カラムは、`wp:media-text` ではなくこのリッチカラムを使うこと。

```html
<!-- wp:loos/columns {"className":"dp-〇〇-columns"} -->
<div class="swell-block-columns dp-〇〇-columns"><div class="swell-block-columns__inner">

  <!-- wp:loos/column -->
  <div class="swell-block-column swl-has-mb--s">
    <!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
    <figure class="wp-block-image size-large">
      <img
        src="http://localhost:10054/wp-content/uploads/2026/03/no-image_dammy.webp"
        alt="画像の説明"
        class="wp-image-dummy"
      />
    </figure>
    <!-- /wp:image -->
  </div>
  <!-- /wp:loos/column -->

  <!-- wp:loos/column -->
  <div class="swell-block-column swl-has-mb--s">
    <!-- ★ ここにテキストコンテンツを入れる -->
  </div>
  <!-- /wp:loos/column -->

</div></div>
<!-- /wp:loos/columns -->
```

---

## TYPE-K: 標準リストブロック（wp:list）

**使用場面:** 箇条書きリスト。ブロックインサーターから挿入する標準リストブロックを使用する。
**カスタマイズ可能箇所（GUI）:** リストアイテムのテキスト追加・削除・編集
**dp-クラスの付与位置:** `dp-list-〇〇` は**明示的に指定された場合のみ** `wp-block-list` に付与する。指定なしの場合はクラスを付与しない。

> ⚠️ `dp-list-〇〇` クラスを付与する場合は `_c-list.scss` へのSCSS実装が必要。指定がない場合はクラスなしで出力すること。

```html
<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>サンプルのリストアイテムテキストです。サンプルのリストアイテムテキストです。</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>サンプルのリストアイテムテキストです。サンプルのリストアイテムテキストです。</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>サンプルのリストアイテムテキストです。サンプルのリストアイテムテキストです。</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
```

**dp-クラスを付与する場合（例）:**
```html
<!-- wp:list {"className":"dp-list-〇〇"} -->
<ul class="wp-block-list dp-list-〇〇"><!-- wp:list-item -->
<li>サンプルのリストアイテムテキストです。</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
```

---

## TYPE-L: タブブロック（wp:loos/tab）

**使用場面:** タブ切り替えコンテンツ。複数のコンテンツを同一エリアに収めたい場合に使用する。
**カスタマイズ可能箇所（GUI）:** タブコンテンツの追加削除・初期表示タブ番号・タブタイトルの配置や幅・配色
**dp-クラスの付与位置:** `wp:loos/tab` の `className` に `dp-tab-block` を付与する。タブスタイルのクラスはエディタ選択で自動付与される。

> ⚠️ 同一ページに複数タブを使用する場合は、タブブロックごとに `tabId` を別々の値にすること。1つのみの場合は `tabId` の流用可。

**タブ種別とクラスの対応:**

| タブ種別 | 自動付与クラス | `wp:loos/tab` の `className` |
|---|---|---|
| ノーマルタブ | `is-style-default` | `"dp-tab-block is-style-default"` |
| 下線タブ | `is-style-bb` | `"dp-tab-block is-style-bb"` |

**ノーマルタブ（3タブ構成の基本形）:**
```html
<!-- wp:loos/tab {"tabId":"752a9db7","tabHeaders":["タブタイトル1","タブタイトル2","タブタイトル3"],"className":"dp-tab-block is-style-default"} -->
<div class="c-tab is-style-default dp-tab-block"><div class="c-tab__inner">
<div class="c-tab__head"><ul class="c-tab__nav"><li class="c-tab__navItem is-active">タブタイトル1</li><li class="c-tab__navItem">タブタイトル2</li><li class="c-tab__navItem">タブタイトル3</li></ul></div>
<div class="c-tab__body">
<!-- wp:loos/tab-body {"id":0,"tabId":"752a9db7"} -->
<div id="tab-752a9db7-0" class="c-tabBody__item" aria-hidden="false"><!-- wp:paragraph -->
<p>ここにタブコンテンツを入れる。標準機能でどのようなブロックでも適用可能。</p>
<!-- /wp:paragraph --></div>
<!-- /wp:loos/tab-body -->

<!-- wp:loos/tab-body {"id":1,"tabId":"752a9db7"} -->
<div id="tab-752a9db7-1" class="c-tabBody__item" aria-hidden="true"><!-- wp:paragraph -->
<p>ここにタブコンテンツを入れる。標準機能でどのようなブロックでも適用可能。</p>
<!-- /wp:paragraph --></div>
<!-- /wp:loos/tab-body -->

<!-- wp:loos/tab-body {"id":2,"tabId":"752a9db7"} -->
<div id="tab-752a9db7-2" class="c-tabBody__item" aria-hidden="true"><!-- wp:paragraph -->
<p>ここにタブコンテンツを入れる。標準機能でどのようなブロックでも適用可能。</p>
<!-- /wp:paragraph --></div>
<!-- /wp:loos/tab-body -->
</div></div></div>
<!-- /wp:loos/tab -->
```

**下線タブに変更する場合:** `is-style-default` → `is-style-bb` に差し替えるだけでよい。
```html
<!-- wp:loos/tab {"tabId":"752a9db7","tabHeaders":["タブタイトル1","タブタイトル2","タブタイトル3"],"className":"dp-tab-block is-style-bb"} -->
```

---

## TYPE-M: ステップブロック（wp:loos/step）

**使用場面:** 流れ・手順・ステップを番号付きで表示するセクション。TYPE-Eの後継（現行API）。
**カスタマイズ可能箇所（GUI）:** ステップ数の追加削除・ステップラベルテキスト・スタイル種別（ノーマル／ビッグ／スモール）・初期表示設定
**dp-クラスの付与位置:** `wp:loos/step` を囲む最外郭（TYPE-A）の `className` に付与する。
**SCSSの追加実装先:** `scss/_c-step-list.scss`

> ⚠️ **H3タイトルについて:** `.swell-block-step__title` はSCSSで非表示にするケースがある。デザインとSWELL標準構造が合わない場合は、`swell-block-step__body` 内にH3ブロックを入れてタイトルを実装すること。H3なしのケース（テキストのみ）も存在する。

**ステップ数字スタイルの種類（`data-num-style` 属性）:**

| スタイル | data-num-style 値 | 見た目 |
|---|---|---|
| サークル（デフォルト） | `circle` | 円形背景に番号 |
| その他 | エディタで選択 | GUIパネルで切り替え可 |

```html
<!-- wp:loos/step -->
<div class="swell-block-step" data-num-style="circle"><!-- wp:loos/step-item {"stepLabel":"STEP"} -->
<div class="swell-block-step__item"><div class="swell-block-step__number u-bg-main"><span class="__label">STEP</span></div><div class="swell-block-step__title u-fz-l">ここにタイトル</div><div class="swell-block-step__body"><!-- wp:paragraph -->
<p>ここにステップコンテンツを入れる。実装イメージのデザインとSWELL標準HTML構造が適合しない場合は、このコンテンツ内の指定位置にH3タイトルを入れる。そもそもH3タイトルを入れないケースもある。</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:loos/step-item -->

<!-- wp:loos/step-item {"stepLabel":"STEP"} -->
<div class="swell-block-step__item"><div class="swell-block-step__number u-bg-main"><span class="__label">STEP</span></div><div class="swell-block-step__title u-fz-l">ここにタイトル</div><div class="swell-block-step__body"><!-- wp:paragraph -->
<p>ここにステップコンテンツを入れる。ステップのスタイルは他に「ビッグ」「スモール」に切り替え可能。</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:loos/step-item -->

<!-- wp:loos/step-item {"stepLabel":"STEP"} -->
<div class="swell-block-step__item"><div class="swell-block-step__number u-bg-main"><span class="__label">STEP</span></div><div class="swell-block-step__title u-fz-l">ここにタイトル</div><div class="swell-block-step__body"><!-- wp:paragraph -->
<p>ここにステップコンテンツを入れる。ステップのアイテム数は追加削除でGUI編集可能。</p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:loos/step-item --></div>
<!-- /wp:loos/step -->
```

**HTML構造の要点:**

| 要素 | クラス | 備考 |
|---|---|---|
| ステップ外枠 | `.swell-block-step` | `data-num-style` でナンバースタイルを指定 |
| 各アイテム | `.swell-block-step__item` | ステップ1件分 |
| ナンバー | `.swell-block-step__number u-bg-main` | `.__label` でラベルテキスト（"STEP"等） |
| 標準タイトル | `.swell-block-step__title u-fz-l` | SCSSで非表示にするケースあり |
| コンテンツ | `.swell-block-step__body` | 段落・H3・画像など自由に追加可 |

---

## 組み合わせパターン（よく使うセット）

| セクション名                                     | 組み合わせ                        |
| ------------------------------------------------ | --------------------------------- |
| 選ばれる理由（カード型）                         | TYPE-A ＋ TYPE-B ＋ TYPE-C        |
| 選ばれる理由（ジグザグ）                         | TYPE-A ＋ TYPE-B ＋ TYPE-D × 複数 |
| 流れ・ステップ                                   | TYPE-A ＋ TYPE-B ＋ TYPE-E        |
| よくある質問                                     | TYPE-A ＋ TYPE-B ＋ TYPE-F        |
| CTA                                              | TYPE-A ＋ TYPE-G                  |
| よくある悩み（左テキスト＋右グリッド＋吹き出し） | TYPE-A ＋ TYPE-H ＋ TYPE-G        |
| タブ切り替えコンテンツ                           | TYPE-A ＋ TYPE-B ＋ TYPE-L        |
| 流れ・ステップ（現行）                           | TYPE-A ＋ TYPE-B ＋ TYPE-M        |
