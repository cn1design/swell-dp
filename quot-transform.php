<?php
/**
 * Template Name: Quotコード変換ツール
 */
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pattern Code Converter</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Syne:wght@400;700;800&display=swap');

    :root {
        --bg: #0e0e0e;
        --surface: #161616;
        --surface2: #1e1e1e;
        --border: #2a2a2a;
        --accent: #c8f03a;
        --accent-dim: #8aab1a;
        --text: #e8e8e8;
        --text-muted: #666;
        --mono: 'DM Mono', monospace;
        --sans: 'Syne', sans-serif;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        background: var(--bg);
        color: var(--text);
        font-family: var(--sans);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 48px 24px 80px;
    }

    /* ヘッダー */
    header {
        width: 100%;
        max-width: 800px;
        margin-bottom: 48px;
    }

    .eyebrow {
        font-family: var(--mono);
        font-size: 11px;
        letter-spacing: 3px;
        color: var(--accent);
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    h1 {
        font-size: clamp(28px, 5vw, 42px);
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -1px;
    }

    h1 span {
        color: var(--accent);
    }

    .subtitle {
        margin-top: 12px;
        font-family: var(--mono);
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
    }

    /* メインカード */
    .card {
        width: 100%;
        max-width: 800px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
    }

    /* セクション */
    .section {
        padding: 28px 32px;
        border-bottom: 1px solid var(--border);
    }

    .section:last-child {
        border-bottom: none;
    }

    .section-label {
        font-family: var(--mono);
        font-size: 11px;
        letter-spacing: 2px;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-label::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 6px;
        background: var(--accent);
        border-radius: 50%;
    }

    textarea {
        width: 100%;
        background: var(--surface2);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text);
        font-family: var(--mono);
        font-size: 12px;
        line-height: 1.7;
        padding: 16px;
        resize: vertical;
        outline: none;
        transition: border-color 0.2s;
    }

    textarea:focus {
        border-color: var(--accent-dim);
    }

    textarea::placeholder {
        color: var(--text-muted);
    }

    #input-code {
        min-height: 200px;
    }

    #output-code {
        min-height: 80px;
    }

    /* ボタンエリア */
    .action-area {
        padding: 24px 32px;
        display: flex;
        align-items: center;
        gap: 16px;
        background: var(--surface2);
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
    }

    .btn-convert {
        flex-shrink: 0;
        background: var(--accent);
        color: #0e0e0e;
        border: none;
        border-radius: 8px;
        font-family: var(--sans);
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 12px 28px;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
    }

    .btn-convert:hover {
        background: #d9ff3f;
    }

    .btn-convert:active {
        transform: scale(0.97);
    }

    .btn-clear {
        background: transparent;
        color: var(--text-muted);
        border: 1px solid var(--border);
        border-radius: 8px;
        font-family: var(--mono);
        font-size: 12px;
        padding: 12px 20px;
        cursor: pointer;
        transition: color 0.15s, border-color 0.15s;
    }

    .btn-clear:hover {
        color: var(--text);
        border-color: #444;
    }

    .status {
        font-family: var(--mono);
        font-size: 12px;
        color: var(--text-muted);
        margin-left: auto;
    }

    .status.ok {
        color: var(--accent);
    }

    /* コピーボタン */
    .output-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .btn-copy-output {
        background: transparent;
        color: var(--accent);
        border: 1px solid var(--accent-dim);
        border-radius: 6px;
        font-family: var(--mono);
        font-size: 11px;
        letter-spacing: 1px;
        padding: 6px 14px;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
    }

    .btn-copy-output:hover {
        background: var(--accent);
        color: #0e0e0e;
    }

    /* 使い方 */
    .howto {
        width: 100%;
        max-width: 800px;
        margin-top: 32px;
        padding: 24px 32px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
    }

    .howto-title {
        font-family: var(--mono);
        font-size: 11px;
        letter-spacing: 2px;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 16px;
    }

    .steps {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .step {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        font-family: var(--mono);
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.6;
    }

    .step-num {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        background: var(--surface2);
        border: 1px solid var(--border);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: var(--accent);
        font-weight: 500;
    }
    </style>
</head>

<body>

    <header>
        <p class="eyebrow">SWELL Pattern Library — Tool</p>
        <h1>Block Code<br><span>Converter</span></h1>
        <p class="subtitle">
            コードエディタからコピーしたブロックコードを貼り付けると<br>
            [pattern_copy code="..."] 形式のショートコードを生成します
        </p>
    </header>

    <div class="card">

        <!-- INPUT -->
        <div class="section">
            <p class="section-label">Input — コードエディタからコピーしたコード</p>
            <textarea id="input-code"
                placeholder="<!-- wp:loos/full-wide {&quot;bgOpacity&quot;:0 ...} -->&#10;<div class=&quot;swell-block-fullWide ...&quot;>&#10;  ...&#10;</div>&#10;<!-- /wp:loos/full-wide -->"></textarea>
        </div>

        <!-- ACTION -->
        <div class="action-area">
            <button class="btn-convert" onclick="convert()">▶ 変換する</button>
            <button class="btn-clear" onclick="clearAll()">クリア</button>
            <span class="status" id="status">—</span>
        </div>

        <!-- OUTPUT -->
        <div class="section">
            <div class="output-header">
                <p class="section-label" style="margin-bottom:0">Output — ショートコード</p>
                <button class="btn-copy-output" onclick="copyOutput()">COPY</button>
            </div>
            <textarea id="output-code" readonly placeholder="変換後のショートコードがここに出力されます"></textarea>
        </div>

    </div>

    <!-- 使い方 -->
    <div class="howto">
        <p class="howto-title">How to use</p>
        <div class="steps">
            <div class="step"><span class="step-num">1</span><span>WPコードエディタで対象ブロックを選択 → 「コードエディタ」に切り替えてコピー</span></div>
            <div class="step"><span class="step-num">2</span><span>上の Input エリアに貼り付け</span></div>
            <div class="step"><span class="step-num">3</span><span>「変換する」をクリック</span></div>
            <div class="step"><span class="step-num">4</span><span>Output のショートコードをコピー → WPの「カスタムHTML」ブロックに貼り付け</span>
            </div>
        </div>
    </div>

    <script>
    function convert() {
        const input = document.getElementById('input-code').value.trim();
        const status = document.getElementById('status');

        if (!input) {
            status.textContent = '⚠ コードを入力してください';
            status.className = 'status';
            return;
        }

        // " → &quot; エスケープ（& は変換しない）
        const escaped = input.replace(/"/g, '&quot;');

        const shortcode = `[pattern_copy code="${escaped}"]`;

        document.getElementById('output-code').value = shortcode;
        status.textContent = `✓ 変換完了 — ${input.length.toLocaleString()} 文字`;
        status.className = 'status ok';
    }

    function clearAll() {
        document.getElementById('input-code').value = '';
        document.getElementById('output-code').value = '';
        document.getElementById('status').textContent = '—';
        document.getElementById('status').className = 'status';
    }

    function copyOutput() {
        const output = document.getElementById('output-code');
        if (!output.value) return;
        navigator.clipboard.writeText(output.value).then(() => {
            const btn = document.querySelector('.btn-copy-output');
            btn.textContent = 'COPIED ✓';
            setTimeout(() => {
                btn.textContent = 'COPY';
            }, 2000);
        });
    }

    // Ctrl+Enter で変換
    document.getElementById('input-code').addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') convert();
    });
    </script>

</body>

</html>