# SWELL Block Rules — SCSS アーキテクチャ & ファイル管理

> **Claude Codeへの指示:** このファイルはSCSSの3層構造・ファイル命名・新規作成ルールを定義する。ブロックHTMLテンプレートと実装ガイドは `swell-blocks-dict.md` を参照すること。

---

## 基本方針

デザインパターンは2層構造で管理する。

| 層 | 管理方法 | 変更者 |
|---|---|---|
| **デザインの骨格**（レイアウト・色・間隔） | SCSSで制御 | 制作者（Claude Code経由） |
| **コンテンツの中身**（テキスト・画像・リンク） | GUIエディタで自由編集 | ユーザー（クライアント） |

---

## 3層SCSSアーキテクチャ

```
【Layer 1: c-ファイル（コンポーネント）】
  単体で完結する汎用パーツ。どのセクションにも使い回せる。
  例: c-card-list.scss / c-section-title.scss / c-faq.scss

【Layer 2: p-ファイル（セクション）】
  c-ファイルを組み合わせてLPの各セクションを構成する。
  基本方針: 既存c-ファイルの変数上書き・順序調整・例外対応を優先する。
  例外ルール: c-ファイルの流用だけでは指定デザインが再現できない場合のみ、
             p-ファイル内での独自スタイル実装を行ってよい。
  例: p-problem.scss / p-strength.scss / p-overview.scss

【Layer 3: モディファイア（バリエーション）】
  c-ファイル内で --a / --b / --c などのクラスで管理。
  例: .dp-card-basic（--aなし）/ .dp-card-band（帯付きカウンター）
```

---

## ファイル命名テーブル（現行ファイル一覧）

| ファイル名 | 層 | 役割 |
|---|---|---|
| `_c-section-title.scss` | c- | セクション見出し（H2＋英語サブ） |
| `_c-card-list.scss` | c- | カードリスト（--basic / --band 等） |
| `_c-list.scss` | c- | テキストリスト |
| `_c-button.scss` | c- | ボタン単体 |
| `_c-media-block.scss` | c- | 画像＋テキスト（メディアブロック） |
| `_c-step-list.scss` | c- | 連番ステップ |
| `_c-faq.scss` | c- | FAQアコーディオン |
| `_c-cta-block.scss` | c- | CTAボタンエリア |
| `_c-pricing-table.scss` | c- | 料金テーブル |
| `_c-banner-list.scss` | c- | バナーリスト |
| `_c-post-list.scss` | c- | 投稿一覧 |
| `_c-split-column.scss` | c- | 4:6分割カラム |
| `_c-bridge-copy.scss` | c- | ブリッジコピー（セクション間つなぎ） |
| `_c-header-nav.scss` | c- | ヘッダーナビゲーション |
| `_c-swipe-slider.scss` | c- | 横スワイプスライダー |
| `_c-fade-animation.scss` | c- | フェードインアニメーション |
| `_p-main-visual.scss` | p- | メインビジュアルセクション |
| `_p-problem.scss` | p- | よくある悩みセクション |
| `_p-solution.scss` | p- | ソリューションセクション |
| `_p-strength.scss` | p- | 選ばれる理由セクション |
| `_p-overview.scss` | p- | 概要・コンセプトセクション |
| `_p-voice.scss` | p- | お客様の声セクション |
| `_p-schedule.scss` | p- | スケジュール・料金プランセクション |
| `_dp-help.scss` | dp- | DP詳細ページ ヘルプUI |
| *(新規c-ファイル)* | c- | 単体で完結するパーツは必ず `c-` プレフィックスで作成 |
| *(新規p-ファイル)* | p- | c-の組み合わせで成立するセクションは `p-` プレフィックスで作成 |

---

## 新規ファイル作成ルール

- 該当ファイルが存在しない場合は自動で新規作成すること
- `c-` か `p-` かの判断は上記アーキテクチャ原則に従うこと
- 判断が曖昧な場合は `c-` を優先し、ファイル名を提案してから作成すること

### ファイル配置パス

```
wp-content/themes/swell_child/scss/_〇〇.scss
```

### dp-style.scss への @import 追記

新規ファイルを作成した場合は、`dp-style.scss` の `/*-- 分割セクション デザインパターン --*/` ブロック末尾に追記すること。

```scss
@import "〇〇"; /* 〇〇の説明 */
```

---

## SCSSの記述ルール（要点）

- 親スコープ `.dp-〇〇` でネストし、スタイル汚染を防ぐ
- ネスト内コメントは `//` のみ（`/* */` は禁止）
- `@include mq()` でレスポンシブをネスト内に記述（ファイル末尾まとめ禁止）
- 既存ファイルへの追記の場合は、ファイル末尾に追加すること
