'use strict';

/**
 * build-inline-css.js
 *
 * cn-design.biz のデザインパターン一覧から各パターンのブロックコードを取得し、
 * 使用クラスに対応する CSS を dp-style.css から抽出して
 * output/inline-css/<slug>.html に出力する。
 *
 * 使用方法: node scripts/build-inline-css.js
 */

const https = require('https');
const http  = require('http');
const fs    = require('fs');
const path  = require('path');

// --- 設定 ---
const LIST_URL  = 'https://cn-design.biz/blog/design-patterns/';
const CSS_PATH  = path.resolve(__dirname, '../dp-style.css');
const OUT_DIR   = path.resolve(__dirname, '../output/inline-css');

// ─────────────────────────────────────────
// HTTP フェッチ（リダイレクト対応）
// ─────────────────────────────────────────
function fetchUrl(url, redirectCount = 0) {
  if (redirectCount > 5) return Promise.reject(new Error('Too many redirects'));
  return new Promise((resolve, reject) => {
    const client = url.startsWith('https') ? https : http;
    const req = client.get(url, { headers: { 'User-Agent': 'Mozilla/5.0 (build-inline-css/1.0)' } }, (res) => {
      if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
        const next = new URL(res.headers.location, url).href;
        res.resume();
        return fetchUrl(next, redirectCount + 1).then(resolve).catch(reject);
      }
      if (res.statusCode !== 200) {
        res.resume();
        return reject(new Error(`HTTP ${res.statusCode}: ${url}`));
      }
      const chunks = [];
      res.setEncoding('utf8');
      res.on('data', c => chunks.push(c));
      res.on('end', () => resolve(chunks.join('')));
      res.on('error', reject);
    });
    req.on('error', reject);
  });
}

// ─────────────────────────────────────────
// HTML エンティティデコード
// ─────────────────────────────────────────
function htmlDecode(str) {
  return str
    .replace(/&amp;/g,  '&')
    .replace(/&lt;/g,   '<')
    .replace(/&gt;/g,   '>')
    .replace(/&quot;/g, '"')
    .replace(/&#039;/g, "'")
    .replace(/&#(\d+);/g,       (_, n) => String.fromCharCode(parseInt(n, 10)))
    .replace(/&#x([0-9a-fA-F]+);/g, (_, h) => String.fromCharCode(parseInt(h, 16)));
}

// ─────────────────────────────────────────
// ブロックコード中の全クラス名を抽出
// ─────────────────────────────────────────
function extractClasses(html) {
  const set = new Set();
  const re = /class=["']([^"']+)["']/g;
  let m;
  while ((m = re.exec(html)) !== null) {
    for (const cls of m[1].split(/\s+/)) {
      if (cls) set.add(cls);
    }
  }
  return [...set];
}

// ─────────────────────────────────────────
// CSS を { selector, body } の配列にトークン分割
// @media などは body に内部ルールをそのまま含む
// ─────────────────────────────────────────
function tokenizeCSS(css) {
  const tokens = [];
  let i = 0;
  const len = css.length;

  while (i < len) {
    // 空白スキップ
    while (i < len && /\s/.test(css[i])) i++;
    if (i >= len) break;

    // コメント読み飛ばし
    if (css[i] === '/' && css[i + 1] === '*') {
      const end = css.indexOf('*/', i + 2);
      i = end === -1 ? len : end + 2;
      continue;
    }

    // @charset / @import は行ごとスキップ
    if (css.slice(i, i + 8) === '@charset' || css.slice(i, i + 7) === '@import') {
      while (i < len && css[i] !== ';') i++;
      i++; // skip ;
      continue;
    }

    // セレクタ部分を収集（{ まで）
    let selStart = i;
    while (i < len && css[i] !== '{') i++;
    const selector = css.slice(selStart, i).trim();
    if (i >= len || !selector) break;

    // ボディ部分を収集（対応する } まで、ネスト考慮）
    let depth = 0;
    let bodyStart = i;
    while (i < len) {
      if (css[i] === '{') depth++;
      else if (css[i] === '}') { depth--; if (depth === 0) { i++; break; } }
      i++;
    }
    const body = css.slice(bodyStart, i).trim();

    if (selector) tokens.push({ selector, body });
  }
  return tokens;
}

// ─────────────────────────────────────────
// セレクタがクラスに前方一致するか
// 例: cls="dp-problem-section" なら
//   ".dp-problem-section { }"          ✓
//   ".dp-problem-section-band { }"     ✓（前方一致）
//   ".dp-problem-section .child { }"   ✓
//   カンマ区切りのいずれかが一致すれば ✓
// ─────────────────────────────────────────
function selectorMatchesClass(selector, cls) {
  const prefix = '.' + cls;
  for (const part of selector.split(',')) {
    const s = part.trim();
    if (s === prefix) return true;
    if (
      s.startsWith(prefix + ' ') ||
      s.startsWith(prefix + '.') ||
      s.startsWith(prefix + ':') ||
      s.startsWith(prefix + '[') ||
      s.startsWith(prefix + '>') ||
      s.startsWith(prefix + '+') ||
      s.startsWith(prefix + '~') ||
      s.startsWith(prefix + '-') ||   // .dp-problem-section-band 等
      s.startsWith(prefix + '_')       // .dp-foo__bar 等
    ) return true;
  }
  return false;
}

// ─────────────────────────────────────────
// 対象クラスに対応する CSS ルールを抽出
// ─────────────────────────────────────────
function extractCSSForClasses(css, classes) {
  if (!classes.length) return '';

  const tokens = tokenizeCSS(css);
  const parts  = [];

  for (const { selector, body } of tokens) {
    const isAtRule = selector.startsWith('@');

    if (isAtRule) {
      // @media / @supports の内部ルールを個別チェック
      const inner = tokenizeCSS(body.slice(1, body.length - 1)); // { } を除く
      const matched = inner.filter(({ selector: s }) =>
        classes.some(cls => selectorMatchesClass(s, cls))
      );
      if (matched.length) {
        const innerStr = matched.map(t => `  ${t.selector} ${t.body}`).join('\n');
        parts.push(`${selector} {\n${innerStr}\n}`);
      }
    } else {
      if (classes.some(cls => selectorMatchesClass(selector, cls))) {
        parts.push(`${selector} ${body}`);
      }
    }
  }

  return parts.join('\n');
}

// ─────────────────────────────────────────
// ページ HTML から { slug, blockCode } を抽出
// .pl-btn--copy[data-code] + 隣接 .pl-btn--detail[href]
// ─────────────────────────────────────────
function parsePatterns(html) {
  // li.pl-item 単位で処理（data-code と href を同一アイテム内で対応）
  const patterns = [];

  // アイテムブロックを正規表現で分割
  // pl-btn--copy と pl-btn--detail が同一 li 内にあると仮定
  const itemRe = /<li[^>]*>([\s\S]*?)<\/li>/gi;
  let itemMatch;

  while ((itemMatch = itemRe.exec(html)) !== null) {
    const item = itemMatch[1];

    // data-code 属性
    const codeMatch = item.match(/data-code=["']([^"']*)["']/);
    if (!codeMatch) continue;

    // href から slug
    const hrefMatch = item.match(/class=["'][^"']*pl-btn--detail[^"']*["'][^>]*href=["']([^"']*)["']|href=["']([^"']*)["'][^>]*class=["'][^"']*pl-btn--detail[^"']*["']/);
    if (!hrefMatch) continue;

    const href = hrefMatch[1] || hrefMatch[2];
    const slugMatch = href.replace(/\/$/, '').match(/\/([^/]+)$/);
    const slug = slugMatch ? slugMatch[1] : null;
    if (!slug) continue;

    patterns.push({
      slug,
      blockCode: htmlDecode(codeMatch[1]),
    });
  }

  // li 方式で0件なら、フォールバック: data-code と href を順番に対応
  if (patterns.length === 0) {
    console.warn('  ⚠️  li 方式で取得できませんでした。フォールバック方式で試みます...');

    const codes   = [...html.matchAll(/data-code=["']([^"']*)["']/g)].map(m => m[1]);
    const hrefs   = [...html.matchAll(/class=["'][^"']*pl-btn--detail[^"']*["'][^>]*href=["']([^"']*)["']|href=["']([^"']*)["'][^>]*class=["'][^"']*pl-btn--detail[^"']*["']/g)]
      .map(m => m[1] || m[2]);

    const count = Math.min(codes.length, hrefs.length);
    for (let i = 0; i < count; i++) {
      const href = hrefs[i];
      const slugMatch = href.replace(/\/$/, '').match(/\/([^/]+)$/);
      const slug = slugMatch ? slugMatch[1] : `pattern-${i + 1}`;
      patterns.push({ slug, blockCode: htmlDecode(codes[i]) });
    }
  }

  return patterns;
}

// ─────────────────────────────────────────
// メイン
// ─────────────────────────────────────────
async function main() {
  console.log('📡  デザインパターン一覧を取得中...');
  console.log(`    ${LIST_URL}\n`);
  const html = await fetchUrl(LIST_URL);

  console.log('🔍  パターンデータを解析中...');
  const patterns = parsePatterns(html);
  console.log(`    → ${patterns.length} 件のパターンを検出\n`);

  if (patterns.length === 0) {
    console.error('❌  パターンが見つかりませんでした。');
    console.error('    ページ構造（.pl-btn--copy / .pl-btn--detail）を確認してください。');
    process.exit(1);
  }

  if (!fs.existsSync(CSS_PATH)) {
    console.error(`❌  CSS ファイルが見つかりません: ${CSS_PATH}`);
    console.error('    先に npm run build を実行してください。');
    process.exit(1);
  }

  console.log('📄  dp-style.css を読み込み中...');
  const css = fs.readFileSync(CSS_PATH, 'utf8');
  console.log(`    → ${css.length.toLocaleString()} 文字\n`);

  // 出力ディレクトリ作成
  fs.mkdirSync(OUT_DIR, { recursive: true });

  console.log('⚙️   CSS 抽出・出力中...');
  let ok = 0;
  let warn = 0;

  for (const { slug, blockCode } of patterns) {
    const classes  = extractClasses(blockCode);
    const extracted = extractCSSForClasses(css, classes);

    if (!extracted.trim()) {
      console.warn(`  ⚠️  ${slug}: 対応 CSS なし（classes: ${classes.join(', ')}）`);
      warn++;
    }

    const output = `<!-- wp:html -->\n<style>\n${extracted}\n</style>\n<!-- /wp:html -->`;
    const outPath = path.join(OUT_DIR, `${slug}.html`);
    fs.writeFileSync(outPath, output, 'utf8');

    const lines = extracted ? extracted.split('\n').length : 0;
    console.log(`  ✅  ${slug}.html  (${classes.length} classes → ${lines} lines CSS)`);
    ok++;
  }

  console.log(`\n✨  完了: ${ok} 件出力 / ${warn} 件警告`);
  console.log(`   出力先: ${OUT_DIR}`);
}

main().catch(err => {
  console.error('\n❌  エラー:', err.message);
  process.exit(1);
});
