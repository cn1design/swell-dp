# SWELL-DP スライダー実装ガイド

新しいスライダーパターンを実装する際の設計ルールと注意事項。

---

## 1. SWELL/Gutenberg の DOM 構造（最重要）

`wp:group` ブロックは `layout:{"type":"default"}` の場合、**内部に `inner-blocks` ラッパーを生成する**。

```html
<!-- ブロックコード上の wp:group -->
<div class="wp-block-group dp-xxx js-works-slider">         ← slider 変数
  <div class="wp-block-group__inner-blocks">                ← inner 変数
    <div class="wp-block-group js-works-slider-item">...</div>
    <div class="wp-block-group js-works-slider-item">...</div>
  </div>
</div>
```

### JS での取得
```javascript
var inner = allSlides[0].parentElement;
// → wp-block-group__inner-blocks が取れる（slider 自身ではない）
inner.classList.add('dp-works-slider-inner'); // inner-blocks に付与される
```

---

## 2. CSS セレクター ルール

`dp-works-slider-inner` は `slider` の**子要素**に付与される。

```scss
// ✅ 正しい（子孫セレクター）
.dp-xxx .dp-works-slider-inner { ... }
.dp-xxx .dp-works-slider-inner > .js-works-slider-item { ... }

// ❌ 絶対に使わない（複合クラス = 同一要素への付与を想定）
.dp-xxx.dp-works-slider-inner { ... }
```

---

## 3. アイテム幅の指定ルール

| 手法 | 使いどき | 注意 |
|---|---|---|
| `calc(100% / N)` | `dp-works-slider-inner` に `padding-inline` がない場合 | 100% = inner 全幅 = ✅ 正確 |
| `calc(100vw / N)` | `dp-works-slider-inner` に `padding-inline` がある場合（将来的な実装） | padding 後のコンテンツボックスを回避 |

**`padding-inline` を inner に当てると `%` 幅は「コンテンツボックス（padding 除いた幅）」が基準になり極小になる。**
現在の実装では `dp-works-slider-inner` に `padding-inline` を当てない。

---

## 4. `scale(1.1)` スケールアップの overflow 対策

中央アイテムを拡大すると `overflow: hidden` に切られる。SCSS の `.dp-works-slider-inner` に以下を必ず付与する：

```scss
.dp-works-slider-inner {
    margin-top: -4em;
    margin-bottom: -4em;
    padding: 4em 0;
}
```

これにより上下のクリッピングを回避しつつ、周囲レイアウトへの影響をゼロにする。

---

## 5. ナビゲーション配置パターン

### Type A: サイド矢印（絶対配置）
works-03 で使用。outer wrapper 内に `position: absolute` で左右に配置。

```scss
.dp-works-slider-outer .dp-slider-nav {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    pointer-events: none;
}
.dp-works-slider-outer .dp-slider-nav__prev { left: 0; }
.dp-works-slider-outer .dp-slider-nav__next { right: 0; }
```

### Type C: 下部中央（フロー配置）
works-04 で使用。outer wrapper の下に `display: flex; justify-content: center;` で配置。

```scss
.dp-works-slider-c-outer .dp-slider-nav {
    position: static;
    display: flex;
    justify-content: center;
    gap: 24px;
    padding-top: var(--dp-space-element-sp);
}
```

---

## 6. JS 実装パターン

### 6-1. 非ループ（端で止まる）
works-03 パターン。`updateButtons()` で端フラグを管理。

```javascript
function updateButtons() {
    prevBtn.classList.toggle('is-edge', inner.scrollLeft <= 1);
    nextBtn.classList.toggle('is-edge',
        inner.scrollLeft + inner.clientWidth >= inner.scrollWidth - 1);
}
inner.addEventListener('scroll', function () {
    updateButtons();
    updateActiveItem();
}, { passive: true });
updateButtons();
```

### 6-2. ループ（無限ループ）
works-04 パターン。クローンを先頭・末尾に追加し、スクロール停止後に実アイテム位置へ瞬時ジャンプ。

```javascript
// クローン追加（先頭・末尾）
slides.forEach(function (s) {
    var c = s.cloneNode(true);
    c.setAttribute('aria-hidden', 'true');
    inner.appendChild(c); // 末尾
});
slides.slice().reverse().forEach(function (s) {
    var c = s.cloneNode(true);
    c.setAttribute('aria-hidden', 'true');
    inner.insertBefore(c, inner.firstChild); // 先頭
});

// 初期スクロール位置（先頭クローンをスキップ）
requestAnimationFrame(function () {
    inner.scrollLeft = getScrollAmount() * count;
});

// ループ補正（スクロール停止 80ms 後）
var loopTimer = null;
function checkLoop() {
    var amount = getScrollAmount();
    var loopWidth = amount * count;
    if (inner.scrollLeft < loopWidth) {
        inner.scrollLeft += loopWidth;       // 先頭クローン域 → 末尾実へ
    } else if (inner.scrollLeft >= loopWidth * 2) {
        inner.scrollLeft -= loopWidth;       // 末尾クローン域 → 先頭実へ
    }
}
inner.addEventListener('scroll', function () {
    clearTimeout(loopTimer);
    loopTimer = setTimeout(checkLoop, 80);
    updateActiveItem();
}, { passive: true });
```

---

## 7. `getScrollAmount()` の実装

**クローン追加後は `slides` 配列を使わず、毎回 DOM から取得する。**

```javascript
function getScrollAmount() {
    var items = inner.querySelectorAll(':scope > .js-works-slider-item');
    if (items.length < 2) return inner.clientWidth;
    var r0 = items[0].getBoundingClientRect();
    var r1 = items[1].getBoundingClientRect();
    return Math.abs(r1.left - r0.left);
}
```

---

## 8. ブロックコードのクラス構成

```html
<!-- スライダーコンテナ -->
<div class="wp-block-group dp-{name} js-works-slider">

  <!-- 各カード -->
  <div class="wp-block-group js-works-slider-item">
    <!-- wp:image -->
    <!-- wp:heading level:3 -->
  </div>

</div>
```

- `js-works-slider` → JS トリガー（DOMContentLoaded で querySelector される）
- `js-works-slider-item` → スライドアイテムの識別子
- `dp-{name}` → CSS のルートクラス

---

## 9. inline-css の構成テンプレート

```html
<!-- dp-deps: _c-media-block _c-section-title -->
<!-- wp:html -->
<style>
/* 非スライダー時グリッド */
.dp-{name}:not(.is-slider-active) { display: grid !important; ... }

/* アイテム幅（inner の全幅が 100%） */
.dp-{name} .dp-works-slider-inner > .js-works-slider-item {
    width: calc(100% / {N});
    /* カードスタイル */
}
@media (max-width: 959px) { width: calc(100% / {N_bp}); }
@media (max-width: 599px) { width: calc(100% / {N_sp}); }

/* 中央アイテム拡大（使用する場合） */
.dp-{name} .dp-works-slider-inner > .js-works-slider-item.is-active {
    transform: scale(1.1);
}

/* outer ナビ */
.{OUTER_CLASS} .dp-slider-nav { ... }
.{OUTER_CLASS} .dp-slider-nav__btn { ... }
</style>
<script>
/* _dp-slider-template.js の内容を貼り付け */
</script>
<!-- /wp:html -->
```

---

## 10. 実装チェックリスト

```
□ ブロックコードに js-works-slider クラスを付与した
□ 各カードに js-works-slider-item クラスを付与した
□ inline-css の先頭に <!-- dp-deps: ... --> を記述した
□ CSS セレクターが子孫セレクター（スペースあり）になっている
□ アイテム幅に padding-inline との干渉がない（% か vw を適切に選択）
□ scale(1.1) 使用時に dp-works-slider-inner に margin/padding -4em を設定した
□ ループ使用時に is-edge ボタン制御を削除した
□ getScrollAmount() が slides 配列ではなく DOM クエリを使っている
□ 二重初期化防止ガード（slider.querySelector('.dp-slider-nav')）がある
□ WP 保存後にブラウザのキャッシュをクリアして確認した
```

---

## 11. 既存パターン一覧

| スラッグ | スライダー種別 | ループ | ナビ位置 | 特記 |
|---|---|---|---|---|
| works-03 | 横スクロール（左端から） | なし | 左右サイド | 4枚以上でスライダー起動 |
| works-04 | センタリング左右見切れ | あり | 下部中央 | PC4.5枚/BP2.5枚/SP1.5枚、中央1.1倍 |
