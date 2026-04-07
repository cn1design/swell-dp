<?php
/**
 * 子テーマ カスタマイザー設定
 *
 * SWELLカスタマイザーに子テーマ専用の設定欄を追加する。
 * 将来的にプラグイン化する際はこのファイルごと移行する。
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {

    // =========================================================
    // セクション: 子テーマ フッター設定
    // =========================================================
    $wp_customize->add_section( 'swell_child_footer', [
        'title'       => 'フッター設定（子テーマ）',
        'description' => 'ブログパーツIDは 管理画面 › ブログパーツ の各記事URLで確認できます（例: post=387）',
        'priority'    => 155, // SWELLフッターセクションの直後
    ] );

    // ----- フッターロゴ下 ブログパーツID -----
    $wp_customize->add_setting( 'child_bp_footer_address', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'child_bp_footer_address', [
        'label'       => 'フッターロゴ下 ブログパーツID',
        'description' => '住所・TEL/FAX などを出力するブログパーツのID（0で非表示）',
        'section'     => 'swell_child_footer',
        'type'        => 'number',
        'input_attrs' => [ 'min' => 0, 'step' => 1, 'placeholder' => '例: 387' ],
    ] );

} );


/* =========================================================
 * 見出しフォント追加（h1〜h3 専用 Google Fonts 設定）
 * 基本デザインセクションの最下部に追加
 * ========================================================= */

/**
 * 見出しフォント定義マップ（slug => [label, google_url, font_family]）
 */
function child_heading_fonts(): array {
    return [
        'none'             => 'デフォルト（本文フォントに従う）',
        'zen-maru-gothic'  => 'Zen Maru Gothic（丸ゴシック）',
        'biz-udpgothic'    => 'BIZ UDPGothic（UDゴシック）',
        'shippori-mincho'  => 'Shippori Mincho（しっぽり明朝）',
    ];
}

function child_heading_font_data(): array {
    return [
        'zen-maru-gothic'  => [
            'google_url'  => 'https://fonts.googleapis.com/css2?family=Zen+Maru+Gothic:wght@400;700&display=swap',
            'font_family' => '"Zen Maru Gothic", sans-serif',
        ],
        'biz-udpgothic'    => [
            'google_url'  => 'https://fonts.googleapis.com/css2?family=BIZ+UDPGothic:wght@400;700&display=swap',
            'font_family' => '"BIZ UDPGothic", sans-serif',
        ],
        'shippori-mincho'  => [
            'google_url'  => 'https://fonts.googleapis.com/css2?family=Shippori+Mincho:wght@400;700&display=swap',
            'font_family' => '"Shippori Mincho", "Hiragino Mincho ProN", serif',
        ],
    ];
}

/**
 * カスタマイザー: 基本デザインセクション末尾に見出しフォント設定を追加
 * priority 20 = SWELL の customize_register（デフォルト10）より後に実行
 */
add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {

    $wp_customize->add_setting( 'child_heading_font', [
        'default'           => 'none',
        'sanitize_callback' => function ( $v ) {
            return array_key_exists( $v, child_heading_fonts() ) ? $v : 'none';
        },
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'child_heading_font', [
        'label'       => '見出しフォント（h1〜h3）',
        'description' => 'サイト全体の h1・h2・h3 に適用するフォント。Google Fonts から自動読み込みされます。',
        'section'     => 'swell_section_base_design',
        'type'        => 'select',
        'choices'     => child_heading_fonts(),
        'priority'    => 999,
    ] );

}, 20 );

/**
 * wp_head priority 8: 選択フォントの Google Fonts <link> を出力
 * （SWELL の priority 9 より前に配置して読み込みを早める）
 */
add_action( 'wp_head', function () {
    $slug = get_theme_mod( 'child_heading_font', 'none' );
    $data = child_heading_font_data();
    if ( ! isset( $data[ $slug ] ) ) return;

    // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
    echo '<link href="' . esc_url( $data[ $slug ]['google_url'] ) . '" rel="stylesheet">' . PHP_EOL;
}, 8 );

/**
 * wp_head priority 15: h1〜h3 の font-family を出力
 */
add_action( 'wp_head', function () {
    $slug = get_theme_mod( 'child_heading_font', 'none' );
    $data = child_heading_font_data();
    if ( ! isset( $data[ $slug ] ) ) return;

    $family = $data[ $slug ]['font_family']; // 固定値のため esc_attr 不要（&quot; への変換を避ける）
    echo '<style>h1,h2,h3{font-family:' . $family . '}</style>' . PHP_EOL;
}, 15 );


/**
 * 子テーマ カスタマイザー値取得ヘルパー
 *
 * get_theme_mod() の値を優先し、未設定(0)の場合は定数フォールバックを返す。
 *
 * @param  string $key      カスタマイザー設定キー
 * @param  int    $fallback functions.php の定数フォールバック値
 * @return int    ブログパーツID（0 = 非表示）
 */
function child_get_bp_id( string $key, int $fallback = 0 ): int {
    $val = (int) get_theme_mod( $key, $fallback );
    return $val > 0 ? $val : $fallback;
}
