# 作業環境 復旧ガイド
> 作成日: 2026-04-06 / MacBook Pro 故障・紛失時の完全復旧手順

---

## 1. 現状の復旧可能性 診断

### ✅ 完全復旧可能（GitHub管理済み）

| データ | リポジトリ | ブランチ |
|---|---|---|
| SWELL 子テーマ（swell_child） | `github.com/cn1design/swell-dp` | `main` |
| SWELL プラグイン（swell-dp） | `github.com/cn1design/swell-dp-plugin` | `main` |

### 🔴 消失リスクあり（要即対応）

| データ | 場所 | 状況 |
|---|---|---|
| cc-company（会社ハブ全体） | `~/cc-company/` | **git管理なし** |
| Claude Code カスタムスキル | `~/.claude/commands/*.md` | **git管理なし** |
| Claude Code 設定・hooks | `~/.claude/settings.json` | **git管理なし** |
| 子テーマ 未コミット変更 | ローカルのみ | **未push** |

### 🟡 復旧に手間がかかる

| データ | 復旧方法 | 難易度 |
|---|---|---|
| ローカルWP環境（cndesign2026） | Local by Flywheelバックアップ | 中 |
| WP design_pattern 投稿51件 | WP XML エクスポート | 低 |
| カスタマイザー設定 | 再設定（値はメモ必要） | 低 |
| Node.js / WP-CLI 環境 | 再インストール | 低 |

---

## 2. 今すぐやるべき対応（優先順）

### 【最優先】子テーマの未コミット変更をpush

```bash
cd "/Users/d-hiyoshi/Local Sites/cndesign2026/app/public/wp-content/themes/swell_child"
# /dp-push を実行
```

未コミット対象:
- `functions.php` — dp-page-title.js enqueue追加
- `package.json` — watch:all:poll をnode経由に変更
- `scss/_p-page-title.scss` — visibility:hidden追加
- `javascript/dp-page-title.js` — 新規（ダッシュ除去JS）
- `scripts/watch-dp.mjs` — 新規（sass watch + plugin sync）

### 【最優先】cc-company を GitHub で管理する

```bash
cd ~/cc-company
git init
# .gitignoreを作成（.DS_Store等を除外）
echo ".DS_Store" > .gitignore
git add .
git commit -m "Initial commit: cc-company hub"
gh repo create cn1design/cc-company --private --source=. --remote=origin --push
```

### 【最優先】Claude Code 設定をcc-companyに保管

```bash
# カスタムスキルをcc-companyにコピー
cp -r ~/.claude/commands ~/cc-company/.claude/
cp ~/.claude/settings.json ~/cc-company/.claude/
cp ~/.claude/notify.sh ~/cc-company/.claude/
cd ~/cc-company && git add . && git commit -m "chore: Claude Code設定を追加"
git push
```

### 【推奨】WP をエクスポート

WordPress管理画面 > ツール > エクスポート > すべてのコンテンツ
→ XMLファイルをDropboxまたはcc-companyに保存

### 【推奨】Local サイトのバックアップ

Local by Flywheel > cndesign2026 右クリック > Export
→ `.zip`をDropboxに保存（DB含む完全バックアップ）

---

## 3. 新MacBook Pro 復旧手順

### Step 1: 基本ツールのインストール

```bash
# Homebrewインストール
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# 必須CLIツール
brew install node gh

# WP-CLI
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
mkdir -p ~/.local/bin
mv wp-cli.phar ~/.local/bin/wp-cli.phar
```

### Step 2: Local by Flywheel

1. [localwp.com](https://localwp.com) からインストール
2. バックアップ `.zip` からサイトをインポート
   - Local > File > Import site > `.zip`選択
3. サイトURL: `http://localhost:10054`

> WPバックアップがない場合: 後述「Step 6: WPコンテンツ再構築」を参照

### Step 3: GitHubからコード復元

```bash
# 作業ディレクトリ作成（Local起動後に実行）
WP_ROOT="/Users/{username}/Local Sites/cndesign2026/app/public/wp-content"

# 子テーマ
git clone https://github.com/cn1design/swell-dp.git \
  "$WP_ROOT/themes/swell_child"

# プラグイン
git clone https://github.com/cn1design/swell-dp-plugin.git \
  "$WP_ROOT/plugins/swell-dp"

# cc-company
git clone https://github.com/cn1design/cc-company.git \
  ~/cc-company
```

### Step 4: 子テーマのnpm環境

```bash
cd "/Users/{username}/Local Sites/cndesign2026/app/public/wp-content/themes/swell_child"
npm install
npm run dev  # sass watch 起動
```

### Step 5: Claude Code 環境

```bash
# Claude Code インストール
npm install -g @anthropic-ai/claude-code

# カスタムスキル・設定を復元
cp -r ~/cc-company/.claude/commands ~/.claude/
cp ~/cc-company/.claude/settings.json ~/.claude/
cp ~/cc-company/.claude/notify.sh ~/.claude/
chmod +x ~/.claude/notify.sh
```

### Step 6: WPコンテンツ再構築（バックアップなしの場合）

```bash
# WP管理画面 > ツール > インポート > WordPressインポーター
# エクスポートしたXMLファイルを選択してインポート
```

カスタマイザー設定は手動で再設定（値メモ参照）:
- 固定ページ タイトルエリア > デザインパターン選択
- 背景装飾画像のアップロード

### Step 7: 動作確認チェックリスト

- [ ] Local サイトが起動する
- [ ] `http://localhost:10054` にアクセス可能
- [ ] swell-dp プラグインが有効化されている
- [ ] デザインパターン51件が表示される
- [ ] `npm run dev` でscss watchが動作する
- [ ] `dp-style.css` 変更が `plugins/swell-dp/assets/css/` に自動コピーされる
- [ ] Claude Code (`claude`) コマンドが動作する
- [ ] `/dp-start` スキルが動作する

---

## 4. 重要な設定値メモ

| 設定項目 | 値 |
|---|---|
| Local PHP パス | `/Users/{username}/Library/Application Support/Local/lightning-services/php-8.3.23+0/bin/darwin-arm64/bin/php` |
| Local run ID | `9KCJpke2m`（※バックアップ復元後は変わる可能性あり） |
| WP URL | `http://localhost:10054` |
| GitHub org | `cn1design` |

> **Note**: Local の run ID（PHP_INI パスの一部）はサイト再構築後に変わる。
> `~/.claude/commands/dp-wp-save.md` 等に記載のパスは更新が必要。

---

## 5. 定期バックアップ推奨サイクル

| 対象 | 頻度 | 方法 |
|---|---|---|
| 子テーマ・プラグイン | 作業のたび | `/dp-push` 実行 |
| cc-company | 週1回 | `git add . && git commit && git push` |
| WP XMLエクスポート | 月1回 | WP管理画面 > エクスポート |
| Local バックアップ | 月1回 | Local > Export site |
