# DATA-MIGRATION-SKILL: 複雑なHTML表 → WordPress ブロックテーブル 変換マニュアル

> 既存サイトの複雑なHTMLデータ（ul/li入りの料金表など）を
> WordPressブロックエディタ（Gutenberg）のブロックコード形式に正確に変換するためのルール集。

---

## 1. 変換の基本思想

「表の1セル = WordPressの1行（`<tr>`）」とは限らない。
**既存HTMLの1セルに複数データが詰め込まれている場合は、必ず1項目1行に分解する。**

ユーザーがWordPress上でデータを編集しやすくするため、テーブルはできるだけ細かい単位に分解することが正解。

---

## 2. ソースHTMLのパターン分類

### パターンA: シンプル（1セル1データ）
```html
<td class="column-3"><div class="t-menu">バッテリー交換修理</div><div class="t-price">要確認</div></td>
```
→ そのまま1行に変換:
```html
<tr><td>バッテリー交換修理</td><td>要確認</td></tr>
```

---

### パターンB: 複数データ詰め込み（ul/li形式）
```html
<td class="column-2">
  <div class="t-menu">画面修理</div>
  <div class="t-price">
    <ul>
      <li><p class="ware-k">軽度割れ Aパーツ</p><p class="ware-price">¥15,800</p></li>
      <li><p class="ware-k">重度割れ Aパーツ</p><p class="ware-price">¥18,800</p></li>
    </ul>
  </div>
</td>
```
→ **1 `<li>` = 1 `<tr>`** に分解。項目名はメニュー名 + 括弧でバリアントを補足:
```html
<tr><td>画面修理 (軽度割れ Aパーツ)</td><td>¥15,800</td></tr>
<tr><td>画面修理 (重度割れ Aパーツ)</td><td>¥18,800</td></tr>
```

**命名規則:** `{t-menu の内容} ({ware-k の内容})`

---

## 3. タグ除去ルール（必須）

以下のタグはすべて除去し、純粋テキストのみを `<td>` に入れる:

| 除去対象タグ | 備考 |
|---|---|
| `<ul>` `<li>` | リスト構造タグ |
| `<p>` | 段落タグ |
| `<br>` | 改行タグ |
| `<div>` | ラッパーdiv（class問わず） |
| `<span>` | インライン装飾（基本除去） |
| `class` / `style` 属性 | すべて除去 |

---

## 4. 特殊値の扱い

| ソースの値 | 変換後 |
|---|---|
| `要確認` | そのまま `<td>要確認</td>` |
| `¥15,800` | そのまま `<td>¥15,800</td>` |
| 空文字・空セル | `<td></td>` として残す |
| HTMLエンティティ（`&amp;` 等） | デコードして記述 |

---

## 5. WordPress ブロックコードの出力形式

### wp:table の基本形
```html
<!-- wp:table {"hasFixedLayout":false,"className":"is-style-simple"} -->
<figure class="wp-block-table is-style-simple"><table><tbody><tr><td>項目名</td><td>価格</td></tr>...</tbody></table></figure>
<!-- /wp:table -->
```

**ポイント:**
- `<figure>` タグで囲む（Gutenberg の wp:table 標準出力形式）
- `<thead>` / `<tfoot>` は不要な場合は省略（`<tbody>` のみでよい）
- `hasFixedLayout: false` = 列幅を内容に合わせて自動調整
- `is-style-simple` = SWELLシンプルテーブルスタイル

---

## 6. expanding-grid-block へのネスト構造

料金表を expanding-grid-block に格納する場合のネスト構造:

```
expanding-grid-wrapper (親)
  └── expanding-grid-item (デバイス種別: "iPhone" 等)
       └── expanding-grid-wrapper (子)
            └── expanding-grid-item (機種名: "iPhone 16e" 等)
                 └── wp:table (料金表)
```

ブロックコード形式:
```html
<!-- wp:expanding-grid-block/expanding-grid-wrapper {"mainColor":"#000000","columnsTab":3} -->
<!-- wp:expanding-grid-block/expanding-grid-item {"title":"iPhone"} -->
<!-- wp:expanding-grid-block/expanding-grid-wrapper {"mainColor":"#000000","columnsTab":3} -->
<!-- wp:expanding-grid-block/expanding-grid-item {"title":"iPhone 16e"} -->
<!-- wp:table {"hasFixedLayout":false,"className":"is-style-simple"} -->
<figure class="wp-block-table is-style-simple"><table><tbody>
  <tr><td>修理項目</td><td>価格</td></tr>
</tbody></table></figure>
<!-- /wp:table -->
<!-- /wp:expanding-grid-block/expanding-grid-item -->
<!-- /wp:expanding-grid-block/expanding-grid-wrapper -->
<!-- /wp:expanding-grid-block/expanding-grid-item -->
<!-- /wp:expanding-grid-block/expanding-grid-wrapper -->
```

**InnerBlocks の閉じタグ順序（必須）:**
開いた順の逆順で閉じる。`expanding-grid-item` → `expanding-grid-wrapper` の順。

---

## 7. 変換作業の手順（チェックリスト）

1. **ソースHTMLを読む** — 行（`<tr>`）単位でセルを把握する
2. **各セルのパターンを分類** — シンプル or 複数データ詰め込み（ul/li）
3. **ul/li セルを分解** — 1 `<li>` = 1 `<tr>` に変換。項目名は `メニュー名 (バリアント名)` で補足
4. **装飾タグをすべて除去** — 純粋テキストのみ残す
5. **「要確認」等の特殊テキストをそのまま保持**
6. **wp:table のブロックコメントで囲む**
7. **expanding-grid-item に格納してネスト構造を組む**
8. **閉じタグの順序を確認**（inner → outer の逆順）

---

## 8. よくあるミスと対処法

| ミス | 対処 |
|---|---|
| ul/li を1行にまとめてしまう | 必ず `<li>` の数だけ `<tr>` を作る |
| `<figure>` タグを忘れる | wp:table は必ず `<figure class="wp-block-table ...">` で囲む |
| 閉じタグの順序を間違える | expanding-grid-item → expanding-grid-wrapper の順で閉じる |
| `<br>` タグが残る | すべて除去（テキストが途切れる場合は半角スペースで結合） |
| class属性が残る | `<td>` / `<tr>` に class は付与しない |
