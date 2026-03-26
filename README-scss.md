# SCSS（Dart Sass）恒久コンパイル手順

この子テーマは WordPress が `style.css` を読み込みます。  
そのため **編集は `scss/style.scss`（必要に応じて `scss/*.scss` に分割）に集約**し、Dart Sassで `scss/style.scss → style.css` を自動生成します。

## 初回セットアップ

このフォルダで実行します。

```bash
cd "/Users/d-hiyoshi/Local Sites/cndesign2026/app/public/wp-content/themes/swell_child"
npm install
```

## ふだんの開発（保存のたびに自動コンパイル）

```bash
cd "/Users/d-hiyoshi/Local Sites/cndesign2026/app/public/wp-content/themes/swell_child"
npm run dev
```

- `npm run dev` は `sass --watch` を起動します（止めるときは `Ctrl + C`）。
- `scss/style.scss`（または `scss/*.scss`）を保存すると `style.css` が更新されます。

## 「反映されない/コンパイルされない」時の診断（おすすめ）

```bash
cd "/Users/d-hiyoshi/Local Sites/cndesign2026/app/public/wp-content/themes/swell_child"
npm run doctor
```

このコマンドで以下をまとめて確認します。

- `scss/style.scss` が存在するか
- `functions.php` が `/style.css` を読み込む設定か
- SCSSが実際にコンパイルできるか（できたら `style.css` を書き出し）

### 監視が効かない場合（保険）

ファイル監視の相性で更新が拾えないときは、ポーリング監視を使います。

```bash
npm run watch:css:poll
```

## 1回だけビルド（監視なし）

```bash
cd "/Users/d-hiyoshi/Local Sites/cndesign2026/app/public/wp-content/themes/swell_child"
npm run build
```

## 確認コマンド

```bash
ls -lt scss/style.scss style.css
```

更新時刻が `scss/style.scss` の保存後に `style.css` も新しくなっていればOKです。

