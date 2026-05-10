<?php
/**
 * Template Name: LP - トラッキング設定ガイド
 * Description: GTM・Meta Pixel 実践ガイド（スタンドアロンLP）
 *
 * 設置方法:
 *   1. このファイルを子テーマディレクトリに配置
 *   2. WordPress管理画面 > 固定ページ > 新規追加
 *   3. ページ属性 > テンプレート で「LP - トラッキング設定ガイド」を選択
 *   4. 公開する
 *
 * GTM設定:
 *   - GTM_CONTAINER_ID を実際のコンテナID（GTM-XXXXXXX）に変更してください
 *   - Meta Pixel IDも同様に変更してください（現在はコメントアウト済み）
 */

// GTM コンテナID（必ず書き換えること）
define( 'GTM_CONTAINER_ID', 'GTM-XXXXXXX' );

// セキュリティ: WordPress外からの直接アクセスを防止
defined( 'ABSPATH' ) || exit;

$page_title       = get_the_title() ?: 'GTM・Meta Pixel 実践ガイド';
$page_description = 'LP自社運用を始める前に知っておくべきGTMとMeta Pixelの違い・正しい設置方法・確認チェックリストを解説します。';
$page_url         = get_permalink() ?: home_url( $_SERVER['REQUEST_URI'] );
$gtm_id           = GTM_CONTAINER_ID;

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?php echo esc_html( $page_title ); ?></title>
<meta name="description" content="<?php echo esc_attr( $page_description ); ?>">

<!-- GTM (head) -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $gtm_id ); ?>');</script>
<!-- /GTM (head) -->

<?php wp_head(); ?>

<style>
/* === Reset & Base ===================================================== */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:16px;-webkit-text-size-adjust:100%}
body{font-family:-apple-system,BlinkMacSystemFont,'Hiragino Sans','Hiragino Kaku Gothic ProN','Noto Sans JP',sans-serif;font-size:1rem;line-height:1.75;color:#1a1a1a;background:#f5f4f0}

/* === Layout =========================================================== */
.lp-wrap{max-width:760px;margin:0 auto;padding:2rem 1.25rem 4rem}
.section{margin-bottom:2.5rem}
hr.divider{border:none;border-top:1px solid #e0ddd6;margin:2rem 0}

/* === Typography ======================================================= */
.section-label{font-size:.6875rem;font-weight:600;letter-spacing:.09em;text-transform:uppercase;color:#888;margin-bottom:.625rem}
h1{font-size:1.5rem;font-weight:700;line-height:1.35;color:#111;margin-bottom:1rem}
h2{font-size:1.125rem;font-weight:700;color:#111;margin-bottom:.75rem}
h3{font-size:.9375rem;font-weight:600;color:#111;margin-bottom:.375rem}
p{font-size:.875rem;color:#555;line-height:1.75}

/* === Cards ============================================================ */
.card{background:#fff;border:1px solid #e8e5df;border-radius:12px;padding:1.25rem}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:.75rem}
@media(max-width:540px){.grid-2{grid-template-columns:1fr}}

/* === Badges & Tags ==================================================== */
.badge{display:inline-block;font-size:.6875rem;font-weight:600;padding:.1875rem .625rem;border-radius:99px;margin-bottom:.5rem}
.badge-gtm{background:#dbeeff;color:#0a3d6b}
.badge-pixel{background:#fff0d6;color:#5a3300}
.tag{display:inline-block;font-size:.6875rem;padding:.125rem .5rem;border-radius:4px;margin-right:.25rem}

/* === Alert Boxes ====================================================== */
.box{border-left:3px solid;border-radius:0 8px 8px 0;padding:.875rem 1rem;margin:.75rem 0}
.box p{font-size:.8125rem}
.box-warn{background:#fff8eb;border-color:#f5a623}.box-warn p{color:#5a3300}
.box-danger{background:#fff0f0;border-color:#e24b4a}.box-danger p{color:#4a1010}
.box-ok{background:#edfaf3;border-color:#27ae60}.box-ok p{color:#0d4a24}

/* === Flow Diagram ===================================================== */
.flow-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0;border:1px solid #e8e5df;border-radius:8px;overflow:hidden;margin:1rem 0}
.flow-step{padding:.875rem 1rem;background:#fff;border-right:1px solid #e8e5df}
.flow-step:last-child{border-right:none}
.flow-num{font-size:.6875rem;color:#aaa;margin-bottom:.25rem}
.flow-title{font-size:.8125rem;font-weight:600;color:#111}
.flow-sub{font-size:.75rem;color:#888;margin-top:.1875rem}
@media(max-width:540px){.flow-grid{grid-template-columns:1fr}.flow-step{border-right:none;border-bottom:1px solid #e8e5df}.flow-step:last-child{border-bottom:none}}

/* === Data Flow Visual ================================================= */
.flow-visual{background:#f9f8f5;border-radius:12px;padding:1.25rem;margin:.75rem 0}
.flow-visual-label{font-size:.75rem;color:#888;margin-bottom:.75rem}
.node{display:inline-flex;align-items:center;border-radius:6px;padding:.375rem .75rem;font-size:.8125rem;font-weight:600}
.node-gtm{background:#dbeeff;color:#0a3d6b}
.node-ga4{background:#e4f5e0;color:#1a5c0a}
.node-pixel{background:#fff0d6;color:#5a3300}
.node-gads{background:#f0f0f0;color:#333}
.flow-children{display:flex;gap:.75rem;padding-left:1.25rem;margin-top:.5rem;flex-wrap:wrap}
.flow-child{display:flex;flex-direction:column;align-items:center;gap:.25rem}
.flow-arrow-v{width:1px;height:1rem;background:#ccc;margin:0 auto}
.flow-dest{font-size:.6875rem;color:#888;text-align:center}

/* === Table ============================================================ */
.tbl-wrap{overflow-x:auto;margin-top:.75rem}
table{width:100%;border-collapse:collapse;font-size:.8125rem}
th{text-align:left;padding:.5rem .75rem;background:#f5f4f0;color:#555;font-weight:600;border-bottom:1px solid #e8e5df}
td{padding:.625rem .75rem;border-bottom:1px solid #e8e5df;color:#1a1a1a;vertical-align:top}
tr:last-child td{border-bottom:none}
.sev{font-size:.6875rem;padding:.125rem .5rem;border-radius:4px;white-space:nowrap}
.sev-h{background:#ffe8e8;color:#8b1a1a}
.sev-m{background:#fff8eb;color:#7a4a00}
.sev-l{background:#edfaf3;color:#0d4a24}

/* === Checklist ======================================================== */
.checklist{list-style:none;margin-top:.5rem}
.checklist li{display:flex;align-items:flex-start;gap:.625rem;padding:.625rem 0;border-bottom:1px solid #e8e5df}
.checklist li:last-child{border-bottom:none}
.chk{width:1.125rem;height:1.125rem;border-radius:4px;border:1.5px solid #ccc;flex-shrink:0;margin-top:.125rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s,border-color .15s}
.chk.done{background:#27ae60;border-color:#27ae60;color:#fff;font-size:.6875rem}
.chk-title{font-size:.875rem;font-weight:600;color:#111}
.chk-sub{font-size:.75rem;color:#888;margin-top:.125rem}

/* === Phase Steps ====================================================== */
.phases{display:flex;flex-direction:column;gap:.75rem;margin-top:.5rem}
.phase{display:flex;gap:.75rem}
.phase-dot{width:1.75rem;height:1.75rem;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;flex-shrink:0;margin-top:.125rem}
.phase-dot-1{background:#dbeeff;color:#0a3d6b}
.phase-dot-2{background:#fff0d6;color:#5a3300}
.phase-dot-3{background:#e4f5e0;color:#1a5c0a}

/* === Summary Card ===================================================== */
.summary-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
@media(max-width:540px){.summary-grid{grid-template-columns:1fr}}
.summary-label{font-size:.6875rem;color:#aaa;margin-bottom:.25rem}

/* === Header =========================================================== */
.lp-header{border-bottom:1px solid #e0ddd6;padding-bottom:1.5rem;margin-bottom:2rem}
.lp-header-eyebrow{font-size:.6875rem;font-weight:600;letter-spacing:.09em;text-transform:uppercase;color:#888;margin-bottom:.5rem}
</style>
</head>

<body <?php body_class( 'lp-tracking-guide' ); ?>>

<!-- GTM (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $gtm_id ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- /GTM (noscript) -->

<div class="lp-wrap">

  <!-- Header -->
  <header class="lp-header section">
    <div class="lp-header-eyebrow">LP運用スターターガイド</div>
    <h1>GTM と Meta Pixel<br>何が違って、どう使うか</h1>
    <p>自社LPの運用を始める前に知っておくべき計測の基本。設置ミスが広告費と学習データを壊す理由から、今すぐできる確認方法まで。</p>
  </header>

  <!-- Section 1: 違いを理解する -->
  <div class="section">
    <div class="section-label">基本を理解する</div>
    <h2>GTM と Pixel、何が違うのか</h2>

    <div class="grid-2">
      <div class="card">
        <span class="badge badge-gtm">GTM — Googleタグマネージャー</span>
        <h3>タグを管理する「箱」</h3>
        <p style="margin-top:.5rem">GTM自体は計測しない。GA4やPixelなど各種「計測コード（タグ）」をまとめて管理・設置するための入れ物。</p>
        <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid #e8e5df">
          <div style="font-size:.6875rem;color:#aaa;margin-bottom:.375rem">送り先</div>
          <span class="tag" style="background:#dbeeff;color:#0a3d6b">Google Analytics 4</span>
          <span class="tag" style="background:#e4f5e0;color:#1a5c0a">Google広告</span>
          <span class="tag" style="background:#f0f0f0;color:#333">その他全部</span>
        </div>
      </div>
      <div class="card">
        <span class="badge badge-pixel">Meta Pixel</span>
        <h3>Meta広告専用「センサー」</h3>
        <p style="margin-top:.5rem">訪問者の行動データをMeta広告に直接送る専用ツール。広告の最適化・リターゲティングに使う。</p>
        <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid #e8e5df">
          <div style="font-size:.6875rem;color:#aaa;margin-bottom:.375rem">送り先</div>
          <span class="tag" style="background:#fff0d6;color:#5a3300">Meta広告マネージャー</span>
        </div>
      </div>
    </div>

    <div class="box box-ok">
      <p><strong>身近な例：</strong> GTMは「コンビニの棚」。Pixelは「その棚に置く商品のひとつ」。棚（GTM）があれば商品（各タグ）を自由に出し入れできる。</p>
    </div>
  </div>

  <hr class="divider">

  <!-- Section 2: データの流れ -->
  <div class="section">
    <div class="section-label">仕組みを知る</div>
    <h2>データはどこへ流れるか</h2>

    <div class="flow-visual">
      <div class="flow-visual-label">LP訪問者がページを開いた瞬間</div>
      <div style="margin-bottom:.5rem">
        <span class="node node-gtm">GTM が読み込まれ、中のタグを順番に実行</span>
      </div>
      <div class="flow-children">
        <div class="flow-child">
          <div class="flow-arrow-v"></div>
          <span class="node node-ga4">GA4タグ</span>
          <div class="flow-dest">Googleアナリティクスへ</div>
        </div>
        <div class="flow-child">
          <div class="flow-arrow-v"></div>
          <span class="node node-pixel">Pixelタグ</span>
          <div class="flow-dest">Meta広告へ</div>
        </div>
        <div class="flow-child">
          <div class="flow-arrow-v"></div>
          <span class="node node-gads">Google広告タグ</span>
          <div class="flow-dest">Google広告へ</div>
        </div>
      </div>
    </div>
  </div>

  <hr class="divider">

  <!-- Section 3: 二重設置 -->
  <div class="section">
    <div class="section-label">よくある致命的ミス</div>
    <h2>二重設置すると何が壊れるか</h2>

    <div class="box box-warn">
      <p><strong>典型的なケース：</strong> LP専用テンプレート（.php）と header.php の両方にGTMを書いてしまい、LPだけ2回発火する状態になる。</p>
    </div>

    <div class="tbl-wrap">
      <table>
        <thead>
          <tr>
            <th>壊れる場所</th>
            <th>具体的な被害</th>
            <th>深刻度</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>GA4</td>
            <td>ページビュー・CV数が全部×2になり分析不能</td>
            <td><span class="sev sev-h">高</span></td>
          </tr>
          <tr>
            <td>Meta Pixel</td>
            <td>CV数が2倍に見え、誤ったターゲットに最適化される</td>
            <td><span class="sev sev-h">高</span></td>
          </tr>
          <tr>
            <td>Google広告</td>
            <td>コンバージョン計測が2重になりROAS計算が狂う</td>
            <td><span class="sev sev-m">中</span></td>
          </tr>
          <tr>
            <td>ページ速度</td>
            <td>不要なスクリプトが2回走り表示が遅くなる</td>
            <td><span class="sev sev-l">低</span></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="box box-danger" style="margin-top:1rem">
      <p><strong>ラーニングフェーズの落とし穴：</strong> Meta広告は最初の約50CVで「誰が買うか」を学習します（ラーニングフェーズ）。この期間のデータが2倍になっていると、間違ったオーディエンスに永続的に最適化され、後から直しにくくなります。広告を開始する前に必ず計測確認を済ませてください。</p>
    </div>
  </div>

  <hr class="divider">

  <!-- Section 4: 正しい設置 -->
  <div class="section">
    <div class="section-label">正しい設置ルール</div>
    <h2>LP運用の鉄則</h2>

    <div class="flow-grid">
      <div class="flow-step">
        <div class="flow-num">通常ページ</div>
        <div class="flow-title">header.php</div>
        <div class="flow-sub">GTM 1回のみ ✅</div>
      </div>
      <div class="flow-step">
        <div class="flow-num">LP専用テンプレート</div>
        <div class="flow-title">lp-***.php</div>
        <div class="flow-sub">GTM 1回のみ ✅</div>
      </div>
      <div class="flow-step">
        <div class="flow-num">重複？</div>
        <div class="flow-title">問題なし</div>
        <div class="flow-sub">別HTMLなので各1個が正解</div>
      </div>
    </div>

    <div class="box box-ok">
      <p><strong>ポイント：</strong> 2つのファイルに1つずつ書くのは「重複」ではありません。別々のHTMLページなので、それぞれが独立して1回持つのが正解です。</p>
    </div>
  </div>

  <hr class="divider">

  <!-- Section 5: Pixel 3原則 -->
  <div class="section">
    <div class="section-label">Meta Pixelについて</div>
    <h2>Pixelを正しく使うための3つの基本</h2>

    <div class="phases">
      <div class="phase">
        <div class="phase-dot phase-dot-1">1</div>
        <div>
          <h3>GTMの中にPixelを入れる（推奨）</h3>
          <p>PixelコードをHTMLに直書きするより、GTMで管理する方が後から修正・追加が簡単。コードを触らずにMeta管理画面から設定変更できる。</p>
        </div>
      </div>
      <div class="phase">
        <div class="phase-dot phase-dot-2">2</div>
        <div>
          <h3>「購入完了ページ」にCVタグを設置</h3>
          <p>申し込みや購入が完了したページに「Purchase」または「Lead」イベントを設置する。これが広告最適化の核心データになる。</p>
        </div>
      </div>
      <div class="phase">
        <div class="phase-dot phase-dot-3">3</div>
        <div>
          <h3>ラーニングフェーズ中はデータを汚染しない</h3>
          <p>広告開始直後の約50CVは学習期間。この期間にタグ変更・二重発火・テスト購入の計測が混ざると、ターゲティングが狂う。開始前に計測確認を必ず完了させる。</p>
        </div>
      </div>
    </div>
  </div>

  <hr class="divider">

  <!-- Section 6: チェックリスト -->
  <div class="section">
    <div class="section-label">今すぐできる確認</div>
    <h2>アップロード後の確認チェックリスト</h2>
    <p style="margin-bottom:1rem">広告をオンにする前に、以下を順番に確認してください。</p>

    <ul class="checklist" id="checklist">
      <li>
        <div class="chk" role="checkbox" aria-checked="false" tabindex="0" onclick="toggleCheck(this)" onkeydown="if(event.key==='Enter'||event.key===' ')toggleCheck(this)"></div>
        <div>
          <div class="chk-title">Chrome拡張「Tag Assistant Legacy」を入れる</div>
          <div class="chk-sub">Googleが提供する無料ツール。LPを開くとタグが何回発火しているか一目でわかる</div>
        </div>
      </li>
      <li>
        <div class="chk" role="checkbox" aria-checked="false" tabindex="0" onclick="toggleCheck(this)" onkeydown="if(event.key==='Enter'||event.key===' ')toggleCheck(this)"></div>
        <div>
          <div class="chk-title">LPを開き、GTMが「1回だけ」表示されるか確認</div>
          <div class="chk-sub">2個以上表示 → まだ二重設置。1個のみ → 正常</div>
        </div>
      </li>
      <li>
        <div class="chk" role="checkbox" aria-checked="false" tabindex="0" onclick="toggleCheck(this)" onkeydown="if(event.key==='Enter'||event.key===' ')toggleCheck(this)"></div>
        <div>
          <div class="chk-title">GTM管理画面でプレビューモードを起動、LPのURLを入力</div>
          <div class="chk-sub">「Container Loaded」が1回だけ表示されればOK</div>
        </div>
      </li>
      <li>
        <div class="chk" role="checkbox" aria-checked="false" tabindex="0" onclick="toggleCheck(this)" onkeydown="if(event.key==='Enter'||event.key===' ')toggleCheck(this)"></div>
        <div>
          <div class="chk-title">Meta Events Managerで「テストイベント」送信を確認</div>
          <div class="chk-sub">PageViewイベントが1件だけ届いていれば正常。2件来たら二重設置</div>
        </div>
      </li>
      <li>
        <div class="chk" role="checkbox" aria-checked="false" tabindex="0" onclick="toggleCheck(this)" onkeydown="if(event.key==='Enter'||event.key===' ')toggleCheck(this)"></div>
        <div>
          <div class="chk-title">GA4リアルタイムでLPのPVが1カウントか確認</div>
          <div class="chk-sub">自分で開いて1PVと表示 → 正常</div>
        </div>
      </li>
      <li>
        <div class="chk" role="checkbox" aria-checked="false" tabindex="0" onclick="toggleCheck(this)" onkeydown="if(event.key==='Enter'||event.key===' ')toggleCheck(this)"></div>
        <div>
          <div class="chk-title">全項目確認後に広告をオン</div>
          <div class="chk-sub">ラーニングフェーズ最初の50CVが正確に取れていれば、その後の最適化が安定する</div>
        </div>
      </li>
    </ul>
  </div>

  <hr class="divider">

  <!-- Section 7: まとめ -->
  <div class="section">
    <div class="section-label">まとめ</div>
    <h2>これだけ覚えておく</h2>
    <div class="card">
      <div class="summary-grid">
        <div>
          <div class="summary-label">GTM</div>
          <p>全タグを管理する入れ物。LPには必ず1回だけ設置。</p>
        </div>
        <div>
          <div class="summary-label">Meta Pixel</div>
          <p>Meta広告専用センサー。GTMの中に入れて管理するのが正解。</p>
        </div>
        <div>
          <div class="summary-label">二重設置の兆候</div>
          <p>CV数が異常に多い・GA4のPVが実感と合わない → 真っ先に疑う。</p>
        </div>
        <div>
          <div class="summary-label">運用開始前の絶対確認</div>
          <p>Tag Assistantとプレビューモードで1回発火を目視確認してから広告をオンにする。</p>
        </div>
      </div>
    </div>
  </div>

</div><!-- /.lp-wrap -->

<script>
function toggleCheck(el){
  var checked = el.classList.contains('done');
  if(checked){
    el.classList.remove('done');
    el.textContent='';
    el.setAttribute('aria-checked','false');
  } else {
    el.classList.add('done');
    el.textContent='✓';
    el.setAttribute('aria-checked','true');
  }
}
</script>

<?php wp_footer(); ?>
</body>
</html>
