<?php
/**
 * dp-base-css-data.php
 * 共通ベースCSSのコピーボタンとデータを出力するテンプレート
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ベースCSSコピーエリア（ヒントテキスト + ボタン + 非表示データ）を出力する。
 * archive-design_pattern.php / page-design_pattern_standard.php から呼び出す。
 */
function dp_render_base_css_copy_area() {

    // Step 1で作った生データファイル（txt）の中身を取得する
    $data_file_path = get_theme_file_path( 'inc/dp-base-style-data.txt' );
    $base_code = '';
    if ( file_exists( $data_file_path ) ) {
        $base_code = file_get_contents( $data_file_path );
    }

    // データが空の場合は何も出力しない
    if ( empty( trim( $base_code ) ) ) return;
    ?>
<div class="dp-base-css-wrap">
    <p class="dp-copy-hint top-hint">＼SWELL固定ページ編集画面にコピペ／</p>
    <button type="button" class="dp-bace-css-btn" data-target="dp-base-style-data">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path
                d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H8V7h11v14z" />
        </svg>
        最初にここをコピー
    </button>
    <p class="dp-copy-hint">※これを貼らないとデザインが反映されません</p>
    <label class="dp-base-css-select" title="LPビルダーに追加してまとめてコピー">
        <input type="checkbox" class="dp-base-css-select__input" aria-label="共通CSSをLPビルダーに追加">
        <span class="dp-base-css-select__mark" aria-hidden="true"></span>
        <span class="dp-base-css-select__label">LPビルダーに追加</span>
    </label>
</div>
<textarea id="dp-base-style-data" style="display:none"
    aria-hidden="true"><?php echo esc_textarea( $base_code ); ?></textarea>
<?php
}